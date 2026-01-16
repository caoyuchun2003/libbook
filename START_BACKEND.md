# 启动后端服务

## 快速启动

### 方式一：使用启动脚本（推荐）

```bash
cd backend
./start.sh
```

### 方式二：手动启动

```bash
cd backend

# 1. 安装依赖（如果还没安装）
composer install

# 2. 创建 .env 文件（如果不存在）
cp .env.example .env

# 3. 生成应用密钥
php artisan key:generate

# 4. 启动服务
php artisan serve
```

## 服务地址

- **后端服务**: http://localhost:8000
- **API 端点**: http://localhost:8000/api
- **文档 API**: http://localhost:8000/api/docs

## 数据库配置（可选）

如果需要使用数据库功能，需要：

1. 编辑 `backend/.env` 文件，配置数据库连接：
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=libbook
DB_USERNAME=root
DB_PASSWORD=your_password
```

2. 运行数据库迁移：
```bash
php artisan migrate
```

3. （可选）填充测试数据：
```bash
php artisan db:seed
```

## 常见问题

### 问题：Composer 命令找不到

**解决方案**：
- 确保已安装 Composer
- 或使用完整路径：`/Users/yuchuncao/Library/Application\ Support/Herd/bin//composer`

### 问题：PHP 版本不兼容

**要求**：PHP >= 8.1

**检查版本**：
```bash
php --version
```

### 问题：端口 8000 已被占用

**解决方案**：
```bash
# 使用其他端口
php artisan serve --port=8001
```

然后修改前端 `vite.config.js` 中的代理配置。

### 问题：权限错误

**解决方案**：
```bash
chmod -R 775 storage bootstrap/cache
```

## 停止服务

按 `Ctrl+C` 停止服务。
