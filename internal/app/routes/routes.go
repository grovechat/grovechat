package routes

import (
	"context"
	"grovechat/internal/app/business/demo"
	"grovechat/internal/app/business/lambda"
	"grovechat/internal/app/config"
	"log"
	"log/slog"
	"net/http"
	"os"
	"path/filepath"

	"github.com/dunglas/frankenphp"
	"github.com/dunglas/mercure"
)

// 全局 Mercure Hub
var globalHub *mercure.Hub

// InitMercureHub 初始化全局 Mercure Hub
func InitMercureHub(cfg *config.Config) error {
	dbPath := filepath.Join(cfg.PhpProjectRoot, "storage", "database", "mercure.db")
	if err := os.MkdirAll(filepath.Dir(dbPath), 0755); err != nil {
		return err
	}
	logger := slog.New(slog.NewTextHandler(os.Stdout, &slog.HandlerOptions{
		Level: slog.LevelInfo,
	}))
	subscriberList := mercure.NewSubscriberList(1000) // 最大订阅者数量
	transport, err := mercure.NewBoltTransport(subscriberList, logger, dbPath, "", uint64(60), 1e7)
	if err != nil {
		return err
	}

	// 创建 Hub
	globalHub, err = mercure.NewHub(
		context.Background(),
		mercure.WithAnonymous(),
		mercure.WithSubscriptions(),
		mercure.WithTransport(transport),
	)
	if err != nil {
		return err
	}

	log.Printf("Mercure Hub 已初始化，数据库: %s", dbPath)
	return nil
}

// HandlePHP 返回处理 PHP 请求的 HandlerFunc
// 提前定义好静态资源后缀
var staticExts = map[string]struct{}{
	".css": {}, ".js": {}, ".png": {}, ".jpg": {}, ".jpeg": {},
	".gif": {}, ".svg": {}, ".ico": {}, ".woff": {}, ".woff2": {},
	".ttf": {}, ".pdf": {}, ".txt": {},
}

func HandlePHP(cfg *config.Config) http.HandlerFunc {
	publicPath := filepath.Join(cfg.PhpProjectRoot, "public")
	phpWorkerPath := "/web-worker.php"
	docRootOption := frankenphp.WithRequestDocumentRoot(publicPath, false)

	return func(w http.ResponseWriter, r *http.Request) {
		requestPath := r.URL.Path

		// 静态文件请求
		ext := filepath.Ext(requestPath)
		if _, isStatic := staticExts[ext]; isStatic {
			fullPath := filepath.Join(publicPath, requestPath)
			if info, err := os.Stat(fullPath); err == nil && !info.IsDir() {
				http.ServeFile(w, r, fullPath)
				return
			}
		}

		// PHP 请求
		phpReq := r.Clone(r.Context())
		phpReq.URL.Path = phpWorkerPath

		req, err := frankenphp.NewRequestWithContext(
			phpReq,
			docRootOption,
			frankenphp.WithRequestSplitPath([]string{".php"}),
			frankenphp.WithRequestEnv(map[string]string{
				"REQUEST_URI": r.RequestURI,
			}),
		)

		if err != nil {
			http.Error(w, err.Error(), http.StatusInternalServerError)
			return
		}

		if err := frankenphp.ServeHTTP(w, req); err != nil {
			log.Printf("PHP error: %v", err)
		}
	}
}

func Register(mux *http.ServeMux, cfg *config.Config) {
	// 初始化 Mercure Hub
	if err := InitMercureHub(cfg); err != nil {
		log.Fatalf("初始化 Mercure Hub 失败: %v", err)
	}

	// Go 路由
	mux.HandleFunc("/ping", demo.Ping)

	// Lambda 路由（调用 PHP 方法）
	mux.HandleFunc("/lambda/example", lambda.ExampleHandler(cfg))
	mux.HandleFunc("/lambda/call", lambda.CustomHandler(cfg))

	// Mercure 路由
	mux.Handle("/.well-known/mercure", globalHub)

	// PHP 路由
	mux.HandleFunc("/", HandlePHP(cfg))
}
