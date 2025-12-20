package main

import (
	"flag"
	"fmt"
	"grovechat/internal/app"
	"grovechat/internal/app/config"
	"os"
	"strings"
)

func main() {
	// 检查是否是artisan子命令
	if len(os.Args) > 1 && os.Args[1] == "artisan" {
		if len(os.Args) < 3 {
			fmt.Println("用法: grovechat artisan <command> [args...]")
			os.Exit(1)
		}
		command := strings.Join(os.Args[2:], " ")
		app.RunArtisan(command)
		return
	}

	// 主命令参数
	port := flag.String("port", "", "HTTP端口 (如: 80 或 :80)")
	domain := flag.String("domain", "", "域名，多个用逗号分隔，设置后自动启用HTTPS")
	storagePath := flag.String("storage-path", "", "数据存储路径")
	flag.Parse()

	// 构建配置
	cfg := config.CLIConfig{
		Port:        *port,
		Domain:      *domain,
		StoragePath: *storagePath,
	}

	app.StartWithCLI(cfg)
}
