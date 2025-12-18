package config

import (
    "log"
    "os"
    "path/filepath"
    "strings"

    "github.com/dunglas/frankenphp"
)

type Config struct {
    PhpProjectRoot  string
    PhpEnv          map[string]string
    WebPort         int
    WatchPaths      []string
    LambdaWorkers   frankenphp.Workers
    ScheduleWorkers frankenphp.Workers
    QueueWorkers    frankenphp.Workers
}

func New() *Config {
    cfg := &Config{
        PhpProjectRoot: ".",
        WebPort:        80,
        PhpEnv:         make(map[string]string),
    }
    if frankenphp.EmbeddedAppPath != "" {
        cfg.PhpProjectRoot = frankenphp.EmbeddedAppPath
        dataPath := ResolveDataPath()
        cfg.PhpEnv[`LARAVEL_STORAGE_PATH`] = filepath.Join(dataPath, "storage")
        cfg.PhpEnv[`DB_DATABASE`] = filepath.Join(dataPath, "database", "main.sqlite")
    }
    cfg.WatchPaths = []string{
        cfg.PhpProjectRoot + "/routes",
        cfg.PhpProjectRoot + "/app",
        cfg.PhpProjectRoot + "/config",
    }
    return cfg
}

func ResolveRootPath() string {
    exePath, err := os.Executable()
    if err != nil {
        log.Fatal(err)
    }
    exePath, _ = filepath.EvalSymlinks(exePath)
    if strings.Contains(exePath, "go-build") || strings.Contains(exePath, os.TempDir()) {
        wd, err := os.Getwd()
        if err != nil {
            log.Fatal(err)
        }
        log.Printf("检测到开发模式 (go run)，使用工作目录: %s", wd)
        return wd
    }
    return filepath.Dir(exePath)
}

func ResolveDataPath() string {
    root := ResolveRootPath()
    dataPath := filepath.Join(root, "data")
    ensureDir(filepath.Join(dataPath, "storage"))
    ensureDir(filepath.Join(dataPath, "database"))
    ensureFile(filepath.Join(dataPath, "database", "main.sqlite"))

    return dataPath
}

func ensureDir(path string) {
    if err := os.MkdirAll(path, 0755); err != nil {
        log.Fatalf("无法创建目录 %s: %v", path, err)
    }
}

func ensureFile(path string) {
    if _, err := os.Stat(path); os.IsNotExist(err) {
        log.Printf("创建文件: %s", path)
        file, err := os.Create(path)
        if err != nil {
            log.Fatalf("无法创建文件 %s: %v", path, err)
        }
        err = file.Close()
        if err != nil {
            log.Println(err.Error())
            return
        }
    }
}
