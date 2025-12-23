package app

import (
	"context"
	"fmt"
	"grovechat/internal/app/config"
	"grovechat/internal/app/routes"
	"log"
	"os"
	"path/filepath"
	"runtime"
	"time"

	"github.com/dunglas/frankenphp"
	"github.com/gin-gonic/autotls"
	"github.com/gin-gonic/gin"
	"github.com/robfig/cron/v3"
	"golang.org/x/crypto/acme/autocert"
)

func StartWithCLI(cliCfg config.CLIConfig) {
	cfg := config.New(cliCfg)
	start(cfg)
}

func start(cfg *config.Config) {
	var workerOptions []frankenphp.WorkerOption
	if frankenphp.EmbeddedAppPath == "" {
		workerOptions = append(workerOptions, frankenphp.WithWorkerWatchMode(cfg.WatchPaths))
	}
	workerOptions = append(workerOptions, frankenphp.WithWorkerEnv(cfg.PhpEnv), frankenphp.WithWorkerMaxFailures(0))

	// web配置
	cfg.PhpOption = append(cfg.PhpOption, frankenphp.WithWorkers("web", cfg.PhpProjectRoot+"/public/web-worker.php", runtime.NumCPU()*2, workerOptions...))

	// 定时任务配置
	workers, option := frankenphp.WithExtensionWorkers("schedule", cfg.PhpProjectRoot+"/public/artisan-worker.php", 1, workerOptions...)
	cfg.PhpOption = append(cfg.PhpOption, option)
	cfg.ArtisanWorkers = workers

	// 队列配置
	queueWorkers, queueOption := frankenphp.WithExtensionWorkers("queue", cfg.PhpProjectRoot+"/public/queue-worker.php", runtime.NumCPU(), workerOptions...)
	cfg.PhpOption = append(cfg.PhpOption, queueOption)
	cfg.QueueWorkers = queueWorkers

	// Lambda配置
	workers, option = frankenphp.WithExtensionWorkers("lambda", cfg.PhpProjectRoot+"/public/lambda-worker.php", runtime.NumCPU()*2, workerOptions...)
	cfg.PhpOption = append(cfg.PhpOption, option)
	cfg.LambdaWorkers = workers

	// 初始化frankenphp
	err := frankenphp.Init(cfg.PhpOption...)
	if err != nil {
		log.Fatalln(err.Error())
	}

	// 嵌入式应用首次启动初始化
	if frankenphp.EmbeddedAppPath != "" {
		log.Println("开始执行数据库迁移...")
		runLaravelCommand(cfg.ArtisanWorkers, "migrate --force")

		log.Printf("开始执行optimize...")
		runLaravelCommand(cfg.ArtisanWorkers, "optimize")
	}

	// 设置 Gin 模式
	if frankenphp.EmbeddedAppPath != "" {
		gin.SetMode(gin.ReleaseMode) // 嵌入式应用使用生产模式
	}

	// 注册路由
	router := gin.Default() // gin.Default() 包含 Logger 和 Recovery 中间件
	routes.Register(router, cfg)

	// 启动HTTP/HTTPS服务器
	if len(cfg.ServerNames) > 0 {
		// HTTPS模式（带自动证书）
		log.Printf("启用自动HTTPS模式，域名: %v", cfg.ServerNames)

		// 配置 autocert Manager
		certCachePath := filepath.Join(cfg.StoragePath, "certs")
		m := autocert.Manager{
			Prompt:     autocert.AcceptTOS,
			HostPolicy: autocert.HostWhitelist(cfg.ServerNames...),
			Cache:      autocert.DirCache(certCachePath),
		}

		log.Printf("证书缓存路径: %s", certCachePath)
		log.Println("首次启动需要几秒获取证书...")
		log.Printf("访问: https://%s", cfg.ServerNames[0])

		// 使用 autotls 启动（自动处理 HTTP 重定向和 HTTPS）
		go func() {
			err := autotls.RunWithManager(router, &m)
			if err != nil {
				log.Fatalln(err.Error())
			}
		}()
	} else {
		// 纯HTTP模式
		log.Printf("HTTP模式，监听端口: %s", cfg.HTTPPort)
		go func() {
			if err := router.Run(":" + cfg.HTTPPort); err != nil {
				log.Fatalln(err.Error())
			}
		}()
	}

	// 定时任务配置
	c := cron.New()
	type ScheduleTask struct {
		CronExpression string
		Command        string
	}
	tasks := []ScheduleTask{
		//{"@every 5s", "inspire"},
		//{"@every 1m", "test"},
	}

	for _, task := range tasks {
		_, err := c.AddFunc(task.CronExpression, func() {
			go runLaravelCommand(cfg.ArtisanWorkers, task.Command)
		})
		if err != nil {
			log.Printf("无法添加 Cron 任务 [%s]: %v", task.Command, err)
		} else {
			log.Printf("任务已注册: [%s] -> %s", task.CronExpression, task.Command)
		}
	}

	c.Start()
	log.Println("Go Cron 调度器已启动")

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

// RunArtisan 运行Laravel Artisan命令（CLI模式）
func RunArtisan(cmdArgs []string) {
	cfg := config.New(config.CLIConfig{})

	// 设置环境变量
	for key, value := range cfg.PhpEnv {
		err := os.Setenv(key, value)
		if err != nil {
			log.Printf("无法设置环境变量: %s=%s", key, value)
			return
		}
	}

	// 使用 ExecuteScriptCLI 直接执行，支持完整的交互式 TTY
	artisanScript := cfg.PhpProjectRoot + "/artisan"
	args := append([]string{"artisan"}, cmdArgs...)
	exitCode := frankenphp.ExecuteScriptCLI(artisanScript, args)
	os.Exit(exitCode)
}
