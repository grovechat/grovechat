package app

import (
    "context"
    "fmt"
    "grovechat/cmd/grovechat/config"
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
        frankenphp.WithWorkerWatchMode(cfg.WatchPaths),
        frankenphp.WithWorkerMaxFailures(0),
    ))

    // 定时任务配置
    workers, option := frankenphp.WithExtensionWorkers("schedule", cfg.PhpProjectRoot+"/public/schedule-worker.php", 1,
        frankenphp.WithWorkerWatchMode(cfg.WatchPaths),
        frankenphp.WithWorkerMaxFailures(0),
    )
    cfg.ScheduleWorkers = workers

    // 队列配置
    // todo

    // Lambda配置
    // todo

    // 初始化frankenphp
    options = append(options, option)
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
        {"@every 5s", "inspire"},
        {"@every 1m", "test"},
    }

    for _, task := range tasks {
        cmd := task.Command
        spec := task.CronExpression
        _, err := c.AddFunc(spec, func() {
            go runLaravelCommand(workers, cmd)
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
    // todo

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

func Stop() {
    frankenphp.Shutdown()
}
