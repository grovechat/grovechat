# GroveChat 支持私有化部署的开源客服系统

## 开发

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
