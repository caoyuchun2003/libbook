#!/bin/bash
set -e

echo "等待数据库连接..."
until php artisan migrate:status > /dev/null 2>&1; do
    echo "数据库未就绪，等待 3 秒..."
    sleep 3
done

echo "数据库已就绪，开始初始化..."

# 清除缓存
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 运行数据库迁移
echo "运行数据库迁移..."
php artisan migrate --force

# 如果数据库为空，可以运行种子
# php artisan db:seed --force

echo "后端初始化完成！"
