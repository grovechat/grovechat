# GroveChat 支持私有化部署的开源客服系统

## 开发

创建 .env 文件:

```shell
cp .env.example .env
```

启动开发环境: 

```shell
docker compose up -d
```

进入开发环境：

```shell
docker compose exec app bash
```

先启动前端服务: 

```shell
npm run dev
```

然后再开一个窗口进入docker启动后端服务：

```shell
make run
```

访问: http://localhost:8080

## 部署

下载二进制文件到你喜欢的目录，比如：/opt/grovechat/grovechat-amd64

授予权限：

运行：

```shell
./grovechat-amd64 --port=8080
```

如果需要以80端口运行，请先授权：

```shell
sudo setcap 'cap_net_bind_service=+ep' grovechat-amd64 
```

然后再运行：

```shell
./grovechat-amd64
```

如果要启用https，请在授权后运行：

```shell
./grovechat-amd64 --domain=www.example.com
```

访问：https://www.example.com

