#!/bin/bash

# Docker 快速启动脚本

set -e

echo "=========================================="
echo "  宇春书城系统 - Docker 启动脚本"
echo "=========================================="
echo ""

# 检查 Docker 是否安装
if ! command -v docker &> /dev/null; then
    echo "❌ 错误: Docker 未安装，请先安装 Docker"
    exit 1
fi

# 检查 Docker Compose 是否安装
if ! command -v docker-compose &> /dev/null && ! docker compose version &> /dev/null; then
    echo "❌ 错误: Docker Compose 未安装，请先安装 Docker Compose"
    exit 1
fi

# 检查 .env 文件
if [ ! -f .env ]; then
    echo "⚠️  未找到 .env 文件，正在创建..."
    cat > .env << EOF
APP_NAME=宇春书城
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=libbook
DB_USERNAME=libbook_user
DB_PASSWORD=libbook_password
DB_ROOT_PASSWORD=root_password

FRONTEND_PORT=80
BACKEND_PORT=8000
DB_PORT=3306
EOF
    echo "✅ 已创建 .env 文件，请根据需要修改配置"
    echo ""
fi

# 检查 APP_KEY
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null || grep -q "APP_KEY=$" .env 2>/dev/null; then
    echo "⚠️  APP_KEY 未设置，将在容器启动后自动生成"
    echo ""
fi

echo "🚀 开始构建并启动 Docker 容器..."
echo ""

# 构建并启动
docker-compose up -d --build

echo ""
echo "⏳ 等待服务启动..."
sleep 10

# 检查服务状态
echo ""
echo "📊 服务状态:"
docker-compose ps

echo ""
echo "🔑 生成 APP_KEY（如果未设置）..."
docker-compose exec -T backend php artisan key:generate --force 2>/dev/null || echo "APP_KEY 已存在或容器未就绪"

echo ""
echo "📦 运行数据库迁移..."
docker-compose exec -T backend php artisan migrate --force || echo "迁移失败或已执行"

echo ""
echo "=========================================="
echo "✅ 启动完成！"
echo "=========================================="
echo ""
echo "访问地址:"
echo "  - 前端: http://localhost"
echo "  - 后端 API: http://localhost:8000/api"
echo ""
echo "常用命令:"
echo "  - 查看日志: docker-compose logs -f"
echo "  - 停止服务: docker-compose stop"
echo "  - 重启服务: docker-compose restart"
echo "  - 查看状态: docker-compose ps"
echo ""
