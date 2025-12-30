#!/bin/bash

set -e

# 检测当前平台
CURRENT_ARCH=$(uname -m)
if [[ "$CURRENT_ARCH" == "x86_64" ]]; then
    PLATFORM="amd64"
    DOCKER_PLATFORM="linux/amd64"
    ARCH_NAME="x86_64"
elif [[ "$CURRENT_ARCH" == "aarch64" ]] || [[ "$CURRENT_ARCH" == "arm64" ]]; then
    PLATFORM="arm64"
    DOCKER_PLATFORM="linux/arm64"
    ARCH_NAME="aarch64"
else
    echo "错误: 无法检测当前架构 ($CURRENT_ARCH)"
    exit 1
fi

echo "开始构建 GroveChat 静态二进制..."
echo ""
echo "================================"
echo "构建平台: $PLATFORM ($ARCH_NAME)"
echo "================================"
echo ""

# 构建镜像
echo "步骤 1/3: 构建 Docker 镜像..."

# GITHUB_TOKEN
BUILD_ARGS=""
if [ -n "${GITHUB_TOKEN}" ]; then
    BUILD_ARGS="--build-arg GITHUB_TOKEN=${GITHUB_TOKEN}"
fi

docker build ${BUILD_ARGS} --platform ${DOCKER_PLATFORM} -t grovechat-static-builder -f static-build.Dockerfile .

# 创建临时容器并提取二进制文件
echo ""
echo "步骤 2/3: 提取静态二进制文件..."
docker create --name grovechat-static-tmp grovechat-static-builder

# 提取到 build 目录
mkdir -p build
docker cp grovechat-static-tmp:/work/dist/grovechat-linux-${ARCH_NAME} build/grovechat-${PLATFORM}

# 清理临时容器
echo ""
echo "步骤 3/3: 清理临时容器..."
docker rm grovechat-static-tmp

echo ""
echo "========================================"
echo "构建完成！"
echo "========================================"
echo ""
echo "二进制文件位置: build/grovechat-${PLATFORM}"
echo ""

# 显示文件信息
ls -lh build/grovechat-${PLATFORM}
file build/grovechat-${PLATFORM}
echo ""
echo "测试运行："
echo "chmod +x build/grovechat-${PLATFORM}"
echo "./build/grovechat-${PLATFORM}"
echo ""
