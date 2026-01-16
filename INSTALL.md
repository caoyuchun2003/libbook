# 安装指南

## 快速安装

### 1. 克隆或下载项目

```bash
cd /path/to/libbook
```

### 2. 后端安装

```bash
cd backend

# 安装依赖
composer install

# 复制环境配置文件
cp .env.example .env

# 生成应用密钥
php artisan key:generate

# 配置数据库（编辑 .env 文件）
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=libbook
# DB_USERNAME=root
# DB_PASSWORD=your_password

# 运行数据库迁移
php artisan migrate

# （可选）填充测试数据
php artisan db:seed

# 启动开发服务器
php artisan serve
```

### 3. 前端安装

```bash
cd frontend

# 安装依赖
npm install

# 启动开发服务器
npm run dev
```

### 4. 访问应用

- 前端：http://localhost:5173
- 后端 API：http://localhost:8000/api

### 5. 测试账号

如果运行了数据库填充器，可以使用以下账号登录：

**管理员**
- 邮箱：admin@libbook.com
- 密码：admin123

**普通用户**
- 邮箱：user@libbook.com
- 密码：user123

## 注意事项

1. 确保 PHP >= 8.1 和 Node.js >= 16.x 已安装
2. 确保 MySQL/PostgreSQL 数据库服务正在运行
3. 确保后端和前端服务都正常启动
4. 如果遇到 CORS 问题，检查 `backend/config/cors.php` 配置

## 常见问题

### Composer 安装失败

```bash
# 清除缓存
composer clear-cache
# 重新安装
composer install --no-cache
```

### npm 安装失败

```bash
# 清除缓存
npm cache clean --force
# 删除 node_modules 重新安装
rm -rf node_modules package-lock.json
npm install
```

### 数据库迁移失败

```bash
# 检查数据库连接
php artisan tinker
# 在 tinker 中测试：DB::connection()->getPdo();

# 如果表已存在，可以重置
php artisan migrate:fresh
```
