#!/bin/bash

# 数据库设置脚本

cd "$(dirname "$0")"

echo "🗄️  设置 LibBook 数据库..."
echo ""

# 检查 .env 文件
if [ ! -f .env ]; then
    echo "❌ .env 文件不存在，请先创建 .env 文件"
    exit 1
fi

# 读取数据库配置
DB_CONNECTION=$(grep "^DB_CONNECTION=" .env | cut -d '=' -f2)
DB_DATABASE=$(grep "^DB_DATABASE=" .env | cut -d '=' -f2)
DB_USERNAME=$(grep "^DB_USERNAME=" .env | cut -d '=' -f2)
DB_PASSWORD=$(grep "^DB_PASSWORD=" .env | cut -d '=' -f2)

echo "数据库配置："
echo "  类型: $DB_CONNECTION"
echo "  数据库: $DB_DATABASE"
echo "  用户: $DB_USERNAME"
echo ""

if [ "$DB_CONNECTION" = "sqlite" ]; then
    echo "📦 使用 SQLite 数据库"
    
    # 创建 SQLite 数据库文件
    if [ ! -f database/database.sqlite ]; then
        touch database/database.sqlite
        echo "✅ SQLite 数据库文件已创建"
    else
        echo "✅ SQLite 数据库文件已存在"
    fi
    
    echo ""
    echo "运行迁移..."
    php artisan migrate --force
    
    echo ""
    read -p "是否填充测试数据？(y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        php artisan db:seed --force
        echo "✅ 测试数据已填充"
    fi
    
elif [ "$DB_CONNECTION" = "mysql" ]; then
    echo "📦 使用 MySQL 数据库"
    echo ""
    echo "请确保："
    echo "  1. MySQL 服务正在运行"
    echo "  2. 数据库 '$DB_DATABASE' 已创建"
    echo "  3. 用户 '$DB_USERNAME' 有访问权限"
    echo ""
    
    # 测试连接
    echo "测试数据库连接..."
    php artisan migrate:status > /dev/null 2>&1
    
    if [ $? -eq 0 ]; then
        echo "✅ 数据库连接成功"
        echo ""
        echo "运行迁移..."
        php artisan migrate --force
        
        echo ""
        read -p "是否填充测试数据？(y/n) " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            php artisan db:seed --force
            echo "✅ 测试数据已填充"
        fi
    else
        echo "❌ 数据库连接失败"
        echo ""
        echo "请检查："
        echo "  1. MySQL 服务是否运行"
        echo "  2. 数据库是否已创建"
        echo "  3. .env 文件中的配置是否正确"
        echo ""
        echo "创建数据库命令："
        echo "  mysql -u $DB_USERNAME -p"
        echo "  CREATE DATABASE $DB_DATABASE CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
        exit 1
    fi
else
    echo "⚠️  不支持的数据库类型: $DB_CONNECTION"
    exit 1
fi

echo ""
echo "✅ 数据库设置完成！"
echo ""
echo "测试账号："
echo "  管理员: admin@libbook.com / admin123"
echo "  普通用户: user@libbook.com / user123"
