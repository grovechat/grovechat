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
	"strings"

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
func HandlePHP(cfg *config.Config) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		requestPath := r.URL.Path
		fullPath := filepath.Join(cfg.PhpProjectRoot, "public", requestPath)

		// 静态文件
		if info, err := os.Stat(fullPath); err == nil && !info.IsDir() && !strings.HasSuffix(requestPath, ".php") {
			http.ServeFile(w, r, fullPath)
			return
		}

		// php 动态脚本
		phpReq := r.Clone(r.Context())
		phpReq.URL.Path = "/web-worker.php"

		req, err := frankenphp.NewRequestWithContext(
			phpReq,
			frankenphp.WithRequestDocumentRoot(cfg.PhpProjectRoot+"/public", false),
			frankenphp.WithRequestSplitPath([]string{".php"}),
			frankenphp.WithRequestEnv(map[string]string{
				"REQUEST_URI": r.RequestURI,
			}),
			frankenphp.WithMercureHub(globalHub),
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

	// PHP 路由（默认路由，必须放在最后）
	mux.HandleFunc("/", HandlePHP(cfg))
}
