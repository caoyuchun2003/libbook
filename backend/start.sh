#!/bin/bash

# Laravel 后端服务启动脚本

cd "$(dirname "$0")"

echo "🚀 启动 LibBook 后端服务..."
echo ""

# 检查 .env 文件
if [ ! -f .env ]; then
    echo "⚠️  .env 文件不存在，正在创建..."
    if [ -f .env.example ]; then
        cp .env.example .env
        echo "✅ 已从 .env.example 创建 .env 文件"
    else
        echo "❌ .env.example 文件不存在，请手动创建 .env 文件"
        exit 1
    fi
fi

# 检查 composer 依赖
if [ ! -d vendor ]; then
    echo "📦 安装 Composer 依赖..."
    composer install --no-interaction
    if [ $? -ne 0 ]; then
        echo "❌ Composer 安装失败"
        exit 1
    fi
    echo "✅ Composer 依赖安装完成"
fi

# 检查应用密钥
if ! grep -q "APP_KEY=base64" .env 2>/dev/null; then
    echo "🔑 生成应用密钥..."
    php artisan key:generate --no-interaction
    echo "✅ 应用密钥已生成"
fi

# 检查存储目录权限
echo "📁 检查存储目录权限..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo ""
echo "✅ 准备完成，启动 Laravel 开发服务器..."
echo "📍 服务地址: http://localhost:8000"
echo "📍 API 地址: http://localhost:8000/api"
echo ""
echo "按 Ctrl+C 停止服务"
echo ""

# 启动服务
php artisan serve --host=127.0.0.1 --port=8000
