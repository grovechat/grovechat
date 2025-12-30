# GroveChat

一个支持私有化部署的开源AI客服系统，类似 Intercom，专注于简单易用的部署体验。

## 特性

- **一键运行** - 单个二进制文件，无需安装 PHP、Nginx 等依赖
- **自动 HTTPS** - 内置 Let's Encrypt 自动证书管理
- **开箱即用** - 内嵌数据库（SQLite），零配置启动
- **现代技术栈** - Go + Laravel + Vue 3 混合架构
- **轻量化部署** - 二进制文件约 136MB，包含完整应用

## 技术栈

**后端**
- Go - 应用容器和 HTTP 服务
- FrankenPHP - PHP 运行时嵌入
- Laravel - Web 框架和业务逻辑
- SQLite - 数据存储

**前端**
- Vue 3 + Inertia.js - 单页应用
- Tailwind CSS 4 + reka-ui - UI 样式
- Vite - 构建工具

## 快速开始

### 生产部署

1. 下载对应平台的二进制文件

```bash
# 下载到目录，例如
cd /opt/grovechat
wget https://github.com/grovechat/grovechat/releases/latest/download/grovechat-amd64
chmod +x grovechat-amd64
```

2. 运行（HTTP 模式）

```bash
./grovechat-amd64 --port=8080
```

访问 http://localhost:8080

3. 运行（HTTPS 模式，自动证书）

```bash
# 如果使用 80/443 端口，需要先授权
sudo setcap 'cap_net_bind_service=+ep' grovechat-amd64

# 启动并自动配置 HTTPS
./grovechat-amd64 --domain=app.grovechat.com
```

首次启动会自动申请 SSL 证书，需要几秒钟。访问 https://app.grovechat.com

4. 后台运行

```bash
nohup ./grovechat-amd64 --domain=app.grovechat.com >> /tmp/grovechat.log 2>&1 &
```

### 命令行参数

```bash
artisan tinker                 # 启动 Tinker（可以安装 rlwrap，用 rlwrap ./grovechat-arm64 artisan tinker 启动有更好的交互效果）
--port=8080                    # 指定 HTTP 端口（默认 80）
--domain=example.com           # 指定域名，自动启用 HTTPS（多个域名用逗号分隔）
--storage-path=/data           # 指定数据存储路径（默认 ./storage）
```

## 开发指南

### 环境要求

- **推荐方式**: VS Code + Dev Containers 扩展
- 或者：Docker & Docker Compose + Go 1.25+

### 方式一：使用 VS Code Dev Container（推荐）

这是最简单的开发方式，提供完整的 IDE 集成和自动化环境配置。

#### 前置要求

1. 安装 [VS Code](https://code.visualstudio.com/)
2. 安装 [Docker Desktop](https://www.docker.com/products/docker-desktop/)
3. 安装 VS Code 扩展：[Dev Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers)

#### 快速开始

1. 用 VS Code 打开项目文件夹
2. 按 `F1` 或 `Cmd/Ctrl+Shift+P` 打开命令面板
3. 输入并选择 `Dev Containers: Reopen in Container`
4. 等待容器构建和初始化（首次需要几分钟，会自动安装所有依赖）
5. 初始化完成后，打开两个终端：
   - 终端 1: 运行 `npm run dev` 启动前端
   - 终端 2: 运行 `make run` 启动后端
6. 访问 http://localhost

#### Dev Container 特性

- 自动安装所有开发依赖（Composer、npm、Go modules）
- 自动配置数据库和环境变量
- 内置 PHP、Go、Vue 等语言服务和智能提示
- 持久化缓存（依赖、bash历史等），重建容器不丢失
- 集成调试配置，支持断点调试 PHP 和 Go
- 预配置的任务运行器和代码格式化

#### 常用操作

```bash
# 运行 Laravel Artisan 命令
php artisan migrate
php artisan tinker

# 运行测试
php artisan test

# 格式化代码
npm run format
composer pint

# 清除缓存
php artisan cache:clear
```

### 方式二：传统 Docker Compose 方式

1. 启动 Docker 容器

```bash
docker compose up -d dev
```

2. 进入容器并初始化

```bash
docker compose exec dev bash
npm i
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
```

3. 启动前端开发服务器

```bash
npm run dev
```

4. 新开一个终端，进入容器启动后端

```bash
docker compose exec dev bash
make run
```

访问 http://localhost

### 构建二进制文件

```bash
# 构建当前平台
./build-static.sh

# 构建指定平台
./build-static.sh -p amd64    # x86-64
./build-static.sh -p arm64    # ARM64
./build-static.sh -p all      # 所有平台

# 输出位置
# build/grovechat-amd64
# build/grovechat-arm64
```

## 系统要求

**运行环境**
- Linux (x86-64 或 ARM64)
- 最低 1GB 内存

**支持的操作系统**
- Ubuntu 20.04+
- Debian 11+
- CentOS 8+
- 其他主流 Linux 发行版

## 许可证

MIT License

## 贡献

欢迎提交 Issue 和 Pull Request
