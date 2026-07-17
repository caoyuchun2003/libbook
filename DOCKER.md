# Docker 部署指南

本文档介绍如何使用 Docker 部署宇春书城系统。

## 前置要求

- Docker >= 20.10
- Docker Compose >= 2.0

## 快速开始

### 1. 配置环境变量

复制环境变量示例文件：

```bash
cp .env.docker.example .env.docker
```

编辑 `.env.docker` 文件，设置必要的配置：

```bash
# 生成 Laravel APP_KEY
cd backend
php artisan key:generate --show
# 将生成的 key 复制到 .env.docker 的 APP_KEY
```

### 2. 启动服务

```bash
# 构建并启动所有服务
docker-compose up -d --build

# 查看服务状态
docker-compose ps

# 查看日志
docker-compose logs -f
```

### 3. 初始化数据库

```bash
# 进入后端容器
docker-compose exec backend bash

# 运行数据库迁移
php artisan migrate

# （可选）填充测试数据
php artisan db:seed
```

### 4. 访问应用

- **前端**: http://localhost
- **后端 API**: http://localhost:8000/api
- **MySQL**: localhost:3306

## 服务说明

### 服务列表

- **mysql**: MySQL 8.0 数据库
- **backend**: Laravel PHP-FPM 后端服务
- **nginx**: Nginx 反向代理（用于后端 API）
- **frontend**: Vue.js 前端应用（Nginx 静态文件服务）

### 端口映射

- `80`: 前端应用
- `8000`: 后端 API
- `3306`: MySQL 数据库

可以在 `.env.docker` 中修改端口配置。

## 常用命令

### 启动和停止

```bash
# 启动所有服务
docker-compose up -d

# 停止所有服务
docker-compose stop

# 停止并删除容器
docker-compose down

# 停止并删除容器、卷（注意：会删除数据库数据）
docker-compose down -v
```

### 查看日志

```bash
# 查看所有服务日志
docker-compose logs -f

# 查看特定服务日志
docker-compose logs -f backend
docker-compose logs -f frontend
docker-compose logs -f mysql
```

### 执行命令

```bash
# 进入后端容器
docker-compose exec backend bash

# 在后端容器中执行 Artisan 命令
docker-compose exec backend php artisan migrate
docker-compose exec backend php artisan cache:clear

# 进入 MySQL 容器
docker-compose exec mysql bash

# 连接 MySQL
docker-compose exec mysql mysql -u libbook_user -p libbook
```

### 重建服务

```bash
# 重建特定服务
docker-compose build backend
docker-compose build frontend

# 重建并启动
docker-compose up -d --build backend
```

## 生产环境部署

### 1. 安全配置

- 修改 `.env.docker` 中的默认密码
- 设置强密码：`DB_PASSWORD`、`DB_ROOT_PASSWORD`
- 设置 `APP_DEBUG=false`
- 生成新的 `APP_KEY`

### 2. 使用 HTTPS

在生产环境中，建议使用 Nginx 作为反向代理，配置 SSL 证书：

```nginx
server {
    listen 443 ssl http2;
    server_name your-domain.com;
    
    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;
    
    location / {
        proxy_pass http://localhost:80;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

### 3. 数据备份

定期备份 MySQL 数据：

```bash
# 备份数据库
docker-compose exec mysql mysqldump -u root -p libbook > backup.sql

# 恢复数据库
docker-compose exec -T mysql mysql -u root -p libbook < backup.sql
```

### 4. 性能优化

- 使用 Redis 作为缓存驱动
- 配置 Nginx 缓存
- 启用 PHP OPcache
- 使用 CDN 加速静态资源

## 故障排查

### 查看容器状态

```bash
docker-compose ps
```

### 查看容器日志

```bash
docker-compose logs backend
docker-compose logs frontend
docker-compose logs mysql
```

### 检查网络连接

```bash
# 测试后端连接
docker-compose exec backend curl http://nginx/api/health

# 测试数据库连接
docker-compose exec backend php artisan migrate:status
```

### 重置服务

```bash
# 停止所有服务
docker-compose down

# 删除卷（注意：会删除数据库数据）
docker-compose down -v

# 重新构建并启动
docker-compose up -d --build
```

## 开发环境

### 使用 Docker 进行开发

开发时可以使用 Docker，但建议：

1. 挂载源代码目录（已配置）
2. 使用 `APP_DEBUG=true`
3. 启用热重载（前端）

### 本地开发（不使用 Docker）

如果不想使用 Docker 进行开发，可以：

1. 只启动 MySQL 服务：`docker-compose up -d mysql`
2. 在本地运行后端和前端
3. 配置 `.env` 连接 Docker 中的 MySQL

## 更新应用

```bash
# 拉取最新代码
git pull

# 重建并重启服务
docker-compose up -d --build

# 运行数据库迁移
docker-compose exec backend php artisan migrate

# 清除缓存
docker-compose exec backend php artisan cache:clear
docker-compose exec backend php artisan config:clear
docker-compose exec backend php artisan route:clear
docker-compose exec backend php artisan view:clear
```

## 注意事项

1. **数据持久化**: 数据库数据存储在 Docker 卷中，删除容器不会丢失数据，但删除卷会。
2. **文件权限**: 确保 `backend/storage` 和 `backend/bootstrap/cache` 目录有写权限。
3. **环境变量**: 修改 `.env.docker` 后需要重启服务：`docker-compose restart`
4. **端口冲突**: 确保端口 80、8000、3306 未被占用。

## 支持

如有问题，请查看：
- [Laravel 文档](https://laravel.com/docs)
- [Vue.js 文档](https://vuejs.org/)
- [Docker 文档](https://docs.docker.com/)
