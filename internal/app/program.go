package app

import (
	"context"
	"fmt"
	"grovechat/internal/app/config"
	"grovechat/internal/app/routes"
	"log"
	"net/http"
	"runtime"
	"time"

	"github.com/dunglas/frankenphp"
	"github.com/robfig/cron/v3"
)

func Start() {
	cfg := config.New()
	var options []frankenphp.Option

	// web配置
	options = append(options, frankenphp.WithWorkers("web", cfg.PhpProjectRoot+"/public/web-worker.php", runtime.NumCPU()*2,
		frankenphp.WithWorkerEnv(cfg.PhpEnv),
		frankenphp.WithWorkerWatchMode(cfg.WatchPaths),
		frankenphp.WithWorkerMaxFailures(0),
	))

	// 定时任务配置
	workers, option := frankenphp.WithExtensionWorkers("schedule", cfg.PhpProjectRoot+"/public/schedule-worker.php", 1,
		frankenphp.WithWorkerEnv(cfg.PhpEnv),
		frankenphp.WithWorkerWatchMode(cfg.WatchPaths),
		frankenphp.WithWorkerMaxFailures(0),
	)
	options = append(options, option)
	cfg.ScheduleWorkers = workers

	// 队列配置
	queueWorkers, queueOption := frankenphp.WithExtensionWorkers("queue", cfg.PhpProjectRoot+"/public/queue-worker.php", runtime.NumCPU(),
		frankenphp.WithWorkerEnv(cfg.PhpEnv),
		frankenphp.WithWorkerWatchMode(cfg.WatchPaths),
		frankenphp.WithWorkerMaxFailures(0),
	)
	options = append(options, queueOption)
	cfg.QueueWorkers = queueWorkers

	// Lambda配置
	workers, option = frankenphp.WithExtensionWorkers("lambda", cfg.PhpProjectRoot+"/public/lambda-worker.php", runtime.NumCPU()*2,
		frankenphp.WithWorkerEnv(cfg.PhpEnv),
		frankenphp.WithWorkerWatchMode(cfg.WatchPaths),
		frankenphp.WithWorkerMaxFailures(0),
	)
	options = append(options, option)
	cfg.LambdaWorkers = workers

	// 初始化frankenphp
	err := frankenphp.Init(options...)
	if err != nil {
		log.Fatalln(err.Error())
	}

	// http服务启动
	mux := http.NewServeMux()
	routes.Register(mux, cfg)
	httpServer := &http.Server{
		Addr:    fmt.Sprintf(":%d", cfg.WebPort),
		Handler: mux,
	}
	go func() {
		err = httpServer.ListenAndServe()
		if err != nil {
			log.Fatalln(err.Error())
		}
	}()

	// 定时任务启动
	c := cron.New()
	type ScheduleTask struct {
		CronExpression string
		Command        string
	}
	tasks := []ScheduleTask{
		//{"@every 5s", "inspire"},
		{"@every 1m", "test"},
	}

	for _, task := range tasks {
		cmd := task.Command
		spec := task.CronExpression
		_, err := c.AddFunc(spec, func() {
			go runLaravelCommand(cfg.ScheduleWorkers, cmd)
		})
		if err != nil {
			log.Printf("无法添加 Cron 任务 [%s]: %v", cmd, err)
		} else {
			log.Printf("任务已注册: [%s] -> %s", spec, cmd)
		}
	}

	c.Start()
	log.Println("Go Cron 调度器已启动")

	// 队列启动
	go startQueueWorker(cfg.QueueWorkers)
	log.Println("队列 Worker 已启动")

	select {}
}

// runLaravelCommand
func runLaravelCommand(workers frankenphp.Workers, command string) {
	ctx, cancel := context.WithTimeout(context.Background(), 5*time.Second)
	defer cancel()

	resp, err := workers.SendMessage(ctx, map[string]any{"command": command}, nil)
	if err != nil {
		log.Printf("[向Laravel发送消息失败] 命令: %s, 失败原因: %v", command, err)
		return
	}

	if arr, ok := resp.(frankenphp.AssociativeArray[any]); ok {
		if output, found := arr.Map["output"]; found {
			log.Printf("%v", output)
		} else {
			log.Printf("[运行Laravel命令失败] 命令: %s, 结果不完整: %v", command, arr.Map)
		}
	} else {
		log.Printf("[运行Laravel命令失败] 命令: %s, 类型解析失败: %T", command, resp)
	}
}

// startQueueWorker 启动队列 Worker 循环
func startQueueWorker(workers frankenphp.Workers) {
	ticker := time.NewTicker(100 * time.Millisecond) // 每 100 毫秒检查一次
	defer ticker.Stop()

	consecutiveEmpty := 0
	maxConsecutiveEmpty := 10 // 连续 10 次空闲后降速

	lastError := ""
	errorCount := 0

	for range ticker.C {
		ctx, cancel := context.WithTimeout(context.Background(), 5*time.Second)

		// 不传递 connection，让 PHP 从配置中读取
		resp, err := workers.SendMessage(ctx, map[string]any{}, nil)
		cancel()

		if err != nil {
			log.Printf("[队列 Worker 错误] %v", err)
			continue
		}

		if arr, ok := resp.(frankenphp.AssociativeArray[any]); ok {
			if processed, found := arr.Map["processed"]; found {
				if isProcessed, ok := processed.(bool); ok && isProcessed {
					// 处理了任务，重置空闲计数和错误计数
					consecutiveEmpty = 0
					errorCount = 0
					lastError = ""
					if job, found := arr.Map["job"]; found {
						log.Printf("[队列任务已处理] %v", job)
					}
					// 立即检查下一个任务
					ticker.Reset(10 * time.Millisecond)
				} else {
					// 没有任务，增加空闲计数
					consecutiveEmpty++
					if consecutiveEmpty >= maxConsecutiveEmpty {
						// 降低检查频率
						ticker.Reset(1 * time.Second)
					} else {
						ticker.Reset(100 * time.Millisecond)
					}
				}
			}

			if errMsg, found := arr.Map["error"]; found && errMsg != nil {
				currentError := fmt.Sprintf("%v", errMsg)
				// 只在错误信息变化或者是新错误时才打印
				if currentError != lastError {
					log.Printf("[队列任务处理错误] %v", errMsg)
					lastError = currentError
					errorCount = 1
				} else {
					errorCount++
					// 相同错误连续出现 10 次后，降低检查频率
					if errorCount >= 10 {
						ticker.Reset(5 * time.Second)
					}
				}
			}
		}
	}
}
