#!/bin/bash
# 构建多平台静态二进制

set -e

echo "开始构建 GroveChat 静态二进制（多平台）..."
echo ""

# 确保 buildx builder 存在
if ! docker buildx inspect multiarch-builder &> /dev/null; then
    echo "创建 multiarch-builder..."
    docker buildx create --name multiarch-builder --use
    docker buildx inspect --bootstrap
else
    echo "使用已存在的 multiarch-builder"
    docker buildx use multiarch-builder
fi

# GITHUB_TOKEN
BUILD_ARGS=""
if [ -n "${GITHUB_TOKEN}" ]; then
    BUILD_ARGS="--build-arg GITHUB_TOKEN=${GITHUB_TOKEN}"
fi

echo ""
echo "================================"
echo "构建平台: linux/amd64,linux/arm64"
echo "================================"
echo ""

# 提取二进制文件
mkdir -p build

# 构建并提取 amd64 版本
echo "步骤 1/4: 构建 linux/amd64 镜像..."
docker buildx build \
    ${BUILD_ARGS} \
    --platform linux/amd64 \
    -t grovechat-static-builder:amd64 \
    -f static-build.Dockerfile \
    --load \
    .

echo ""
echo "步骤 2/4: 提取 amd64 二进制..."
docker create --name grovechat-static-amd64 grovechat-static-builder:amd64
docker cp grovechat-static-amd64:/work/dist/grovechat-linux-x86_64 build/grovechat-amd64
docker rm grovechat-static-amd64

# 构建并提取 arm64 版本
echo ""
echo "步骤 3/4: 构建 linux/arm64 镜像..."
docker buildx build \
    ${BUILD_ARGS} \
    --platform linux/arm64 \
    -t grovechat-static-builder:arm64 \
    -f static-build.Dockerfile \
    --load \
    .

echo ""
echo "步骤 4/4: 提取 arm64 二进制..."
docker create --name grovechat-static-arm64 grovechat-static-builder:arm64
docker cp grovechat-static-arm64:/work/dist/grovechat-linux-aarch64 build/grovechat-arm64
docker rm grovechat-static-arm64

echo ""
echo "========================================"
echo "构建完成！"
echo "========================================"
echo ""
echo "二进制文件:"
ls -lh build/grovechat-*
echo ""
file build/grovechat-*
echo ""
echo "测试运行："
echo "chmod +x build/grovechat-amd64 (或 build/grovechat-arm64)"
echo "./build/grovechat-amd64 (或 ./build/grovechat-arm64)"
echo ""
