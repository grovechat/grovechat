#!/bin/bash

set -e

# 显示使用说明
show_usage() {
    echo "用法: $0 [选项]"
    echo ""
    echo "选项:"
    echo "  -p, --platform PLATFORM    指定构建平台 (amd64, arm64, 或 all)"
    echo "  -h, --help                 显示此帮助信息"
    echo ""
    echo "示例:"
    echo "  $0                         # 构建当前平台"
    echo "  $0 -p amd64                # 构建 x86-64 平台"
    echo "  $0 -p arm64                # 构建 ARM64 平台"
    echo "  $0 -p all                  # 构建所有平台"
    echo ""
}

# 解析命令行参数
PLATFORMS=""
while [[ $# -gt 0 ]]; do
    case $1 in
        -p|--platform)
            PLATFORMS="$2"
            shift 2
            ;;
        -h|--help)
            show_usage
            exit 0
            ;;
        *)
            echo "错误: 未知选项 $1"
            show_usage
            exit 1
            ;;
    esac
done

# 如果没有指定平台，使用当前平台
if [ -z "$PLATFORMS" ]; then
    CURRENT_ARCH=$(uname -m)
    if [[ "$CURRENT_ARCH" == "x86_64" ]]; then
        PLATFORMS="amd64"
    elif [[ "$CURRENT_ARCH" == "aarch64" ]] || [[ "$CURRENT_ARCH" == "arm64" ]]; then
        PLATFORMS="arm64"
    else
        echo "错误: 无法检测当前架构 ($CURRENT_ARCH)"
        exit 1
    fi
    echo "未指定平台，使用当前平台: $PLATFORMS"
    echo ""
fi

# 构建单个平台
build_platform() {
    local platform=$1
    local docker_platform=""
    local arch_name=""

    case $platform in
        amd64|x86_64|x86-64)
            docker_platform="linux/amd64"
            arch_name="x86_64"
            ;;
        arm64|aarch64)
            docker_platform="linux/arm64"
            arch_name="aarch64"
            ;;
        *)
            echo "错误: 不支持的平台 $platform"
            echo "支持的平台: amd64, arm64"
            exit 1
            ;;
    esac

    echo "================================"
    echo "构建平台: $platform ($arch_name)"
    echo "================================"
    echo ""

    # 构建镜像
    echo "步骤 1/3: 构建 Docker 镜像..."

    # GITHUB_TOKEN
    BUILD_ARGS=""
    if [ -n "${GITHUB_TOKEN}" ]; then
        BUILD_ARGS="--build-arg GITHUB_TOKEN=${GITHUB_TOKEN}"
    fi

    docker build ${BUILD_ARGS} --platform ${docker_platform} -t grovechat-static-builder-${platform} -f static-build.Dockerfile .

    # 创建临时容器并提取二进制文件
    echo ""
    echo "步骤 2/3: 提取静态二进制文件..."
    docker create --name grovechat-static-tmp-${platform} grovechat-static-builder-${platform}

    # 提取到 build 目录
    mkdir -p build
    docker cp grovechat-static-tmp-${platform}:/work/dist/grovechat-linux-${arch_name} build/grovechat-${platform}

    # 清理临时容器
    echo ""
    echo "步骤 3/3: 清理临时容器..."
    docker rm grovechat-static-tmp-${platform}

    echo ""
    echo "平台 $platform 构建完成！"
    echo "二进制文件位置: build/grovechat-${platform}"
    echo ""

    # 显示文件信息
    ls -lh build/grovechat-${platform}
    file build/grovechat-${platform}
    echo ""
}

# 主程序
echo "开始构建 GroveChat 静态二进制..."
echo ""

# 处理 all 平台
if [ "$PLATFORMS" == "all" ]; then
    PLATFORMS="amd64 arm64"
fi

# 构建所有指定的平台
for platform in $PLATFORMS; do
    build_platform $platform
done

echo ""
echo "========================================"
echo "所有构建完成！"
echo "========================================"
echo ""
echo "生成的文件:"
ls -lh build/grovechat-*
echo ""
echo "测试运行："
echo "chmod +x build/grovechat-<platform>"
echo "./build/grovechat-<platform>"
echo ""
