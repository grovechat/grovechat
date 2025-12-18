package routes

import (
	"grovechat/internal/app/business/demo"
	"grovechat/internal/app/config"
	"log"
	"net/http"
	"os"
	"path/filepath"
	"strings"

	"github.com/dunglas/frankenphp"
)

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
	// Go 路由
	mux.HandleFunc("/ping", demo.Ping)

	// PHP 路由
	mux.HandleFunc("/", HandlePHP(cfg))
}
