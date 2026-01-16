# 启动后端服务指南

## 已完成的准备工作

✅ 已创建核心文件：
- `artisan` - Laravel 命令行工具
- `bootstrap/app.php` - 应用引导文件
- `.env` - 环境配置文件
- 路由文件已配置

## 启动步骤

### 1. 安装依赖

```bash
cd backend
composer install
```

如果遇到 PHP 配置警告，可以忽略（不影响功能）。

### 2. 生成应用密钥

```bash
php artisan key:generate
```

### 3. 启动服务

```bash
php artisan serve
```

或者使用启动脚本：

```bash
./start.sh
```

## 服务地址

启动成功后，服务将在以下地址运行：

- **后端服务**: http://localhost:8000
- **API 端点**: http://localhost:8000/api
- **文档 API**: http://localhost:8000/api/docs

## 验证服务

在浏览器中访问：http://localhost:8000

应该看到 JSON 响应：
```json
{
  "message": "LibBook API",
  "version": "1.0.0",
  "docs": "/api/docs"
}
```

## 常见问题

### PHP 配置警告

如果看到 Herd 相关的 PHP 警告，可以忽略，不影响 Laravel 运行。

### Composer 命令找不到

使用完整路径：
```bash
/Users/yuchuncao/Library/Application\ Support/Herd/bin//composer install
```

### 端口被占用

使用其他端口：
```bash
php artisan serve --port=8001
```

## 下一步

1. 配置数据库（如果需要）
2. 运行数据库迁移：`php artisan migrate`
3. 启动前端服务：`cd ../frontend && npm run dev`
