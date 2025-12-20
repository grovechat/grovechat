package config

import (
	"crypto/rand"
	"encoding/base64"
	"log"
	"os"
	"path/filepath"
	"strings"

	"github.com/dunglas/frankenphp"
)

type Config struct {
	PhpProjectRoot string
	PhpEnv         map[string]string
	WatchPaths     []string
	PhpOption      []frankenphp.Option
	LambdaWorkers  frankenphp.Workers
	ArtisanWorkers frankenphp.Workers
	QueueWorkers   frankenphp.Workers
	StoragePath    string   // 自定义 storage 目录路径
	// HTTPS 配置
	ServerNames   []string // HTTPS域名列表，如：["example.com", "www.example.com"]，留空则使用HTTP模式
	Email         string
	HTTPPort      string // HTTP端口，默认":80"
	HTTPSPort     string // HTTPS端口，默认":443"
	CertCachePath string // 证书缓存路径
}

// CLIConfig CLI参数配置
type CLIConfig struct {
	Port        string
	Domain      string
	Email       string
	StoragePath string
}

func New(cli CLIConfig) *Config {
	cfg := &Config{
		PhpProjectRoot: ".",
		PhpEnv:         make(map[string]string),
		ServerNames:    []string{},
		HTTPPort:       "80",
		HTTPSPort:      "443",
		CertCachePath:  "",
	}

	cfg.PhpEnv[`MAX_REQUESTS`] = "500"
	cfg.PhpOption = append(cfg.PhpOption, frankenphp.WithPhpIni(map[string]string{
		`post_max_size`:       `200M`,
		`upload_max_filesize`: `200M`,
	}))
	if frankenphp.EmbeddedAppPath != "" {
		// 自定义端口号
		if cli.Port != "" {
			cfg.HTTPPort = cli.Port
		}
		// 自定义域名
		if cli.Domain != "" {
			cfg.ServerNames = strings.Split(cli.Domain, ",")
			for i, name := range cfg.ServerNames {
				cfg.ServerNames[i] = strings.TrimSpace(name)
			}
		}
		// 接收https证书过期通知的电子邮件地址
		if cli.Email != "" {
			cfg.Email = cli.Email
		}
		// 确定存储路径
		var storagePath string
		if cli.StoragePath != "" {
			storagePath, _ = filepath.Abs(cli.StoragePath)
		} else {
			rootPath := ResolveRootPath()
			storagePath = filepath.Join(rootPath, "storage")
		}
		storagePath, _ = filepath.Abs(storagePath)
		ensureStorageStructure(storagePath)
		cfg.StoragePath = storagePath
		cfg.CertCachePath = filepath.Join(storagePath, "certs")
		cfg.PhpProjectRoot = frankenphp.EmbeddedAppPath

		// 环境变量
		cfg.PhpEnv[`LARAVEL_STORAGE_PATH`] = storagePath
	}

	// 监听目录
	cfg.WatchPaths = []string{
		cfg.PhpProjectRoot + "/routes",
		cfg.PhpProjectRoot + "/app",
		cfg.PhpProjectRoot + "/config",
		cfg.PhpProjectRoot + "/lang",
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
		return wd
	}
	return filepath.Dir(exePath)
}

func ensureStorageStructure(storagePath string) {
	// Laravel必需的storage目录结构
	ensureDir(filepath.Join(storagePath, "app", "public"))
	ensureDir(filepath.Join(storagePath, "framework", "cache", "data"))
	ensureDir(filepath.Join(storagePath, "framework", "sessions"))
	ensureDir(filepath.Join(storagePath, "framework", "views")) // Blade编译必需
	ensureDir(filepath.Join(storagePath, "logs"))

	// 数据库目录和文件
	ensureDir(filepath.Join(storagePath, "database"))
	ensureFile(filepath.Join(storagePath, "database", "main.sqlite"))
	ensureFile(filepath.Join(storagePath, "database", "session.sqlite"))
	ensureFile(filepath.Join(storagePath, "database", "cache.sqlite"))
	ensureFile(filepath.Join(storagePath, "database", "jobs.sqlite"))

	// 证书目录
	ensureDir(filepath.Join(storagePath, "certs"))

	// 创建.env文件
	if frankenphp.EmbeddedAppPath != "" {
		envPath := filepath.Join(storagePath, ".env")
		if _, err := os.Stat(envPath); os.IsNotExist(err) {
			appKey, err := generateAppKey()
			if err != nil {
				log.Fatalf("无法生成APP_KEY: %v", err.Error())
			}
			if err := os.WriteFile(envPath, []byte(`APP_KEY=`+appKey), 0644); err == nil {
				log.Printf("首次运行：已创建配置文件 %s", envPath)
			} else {
				log.Fatalf("无法写入.env文件 %s", err.Error())
			}
		}
		envData, err := os.ReadFile(envPath)
		if err != nil {
			log.Fatalf("无法读取配置文件.env: %v", err.Error())
		}
		err = os.WriteFile(frankenphp.EmbeddedAppPath+"/.env", envData, 0644)
		if err != nil {
			log.Fatalf("无法写入.env文件 %s", err.Error())
		}
	}
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

func generateAppKey() (string, error) {
	const keyLength = 32
	key := make([]byte, keyLength)
	if _, err := rand.Read(key); err != nil {
		return "", err
	}
	return "base64:" + base64.StdEncoding.EncodeToString(key), nil
}
