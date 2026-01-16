# 使用官方 PHP 8.1 FPM 镜像作为基础镜像
FROM php:8.1-fpm

# 设置工作目录
WORKDIR /var/www/html

# 安装系统依赖
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 安装 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 复制 composer 文件
COPY composer.json composer.lock ./

# 安装 PHP 依赖（不安装 dev 依赖以减小镜像大小）
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# 复制应用代码
COPY . .

# 设置权限
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# 暴露端口
EXPOSE 9000

# 创建启动脚本
RUN echo '#!/bin/bash\n\
set -e\n\
echo "等待数据库连接..."\n\
until php artisan migrate:status > /dev/null 2>&1; do\n\
    echo "数据库未就绪，等待 3 秒..."\n\
    sleep 3\n\
done\n\
echo "数据库已就绪，开始初始化..."\n\
php artisan config:clear\n\
php artisan cache:clear\n\
php artisan route:clear\n\
php artisan view:clear\n\
echo "运行数据库迁移..."\n\
php artisan migrate --force || true\n\
echo "后端初始化完成！"\n\
exec php-fpm' > /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

# 启动 PHP-FPM
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
