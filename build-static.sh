#!/bin/bash

set -e

echo "开始构建 GroveChat 静态二进制..."
echo ""

# 构建镜像
echo "步骤 1/3: 构建 Docker 镜像..."

# GITHUB_TOKEN
BUILD_ARGS=""
if [ -n "${GITHUB_TOKEN}" ]; then
    BUILD_ARGS="--build-arg GITHUB_TOKEN=${GITHUB_TOKEN}"
fi

docker build ${BUILD_ARGS} -t grovechat-static-builder -f static-build.Dockerfile .

# 创建临时容器并提取二进制文件
echo ""
echo "提取静态二进制文件..."
docker create --name grovechat-static-tmp grovechat-static-builder

# 提取到 build 目录
mkdir -p build
# 自动检测架构
ARCH=$(docker run --rm grovechat-static-builder uname -m)
docker cp grovechat-static-tmp:/work/dist/grovechat-linux-${ARCH} build/grovechat

# 清理临时容器
echo ""
echo "清理临时容器..."
docker rm grovechat-static-tmp

echo ""
echo "构建完成！"
echo "二进制文件位置: build/grovechat"
echo ""

# 显示文件信息
ls -lh build/grovechat
file build/grovechat

echo ""
echo "测试运行："
echo "chmod +x build/grovechat"
echo "./build/grovechat"
echo ""
