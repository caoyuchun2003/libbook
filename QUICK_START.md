# 快速启动指南

## 问题排查

如果遇到 `ECONNREFUSED` 或代理错误，通常是因为后端服务未启动。

## 启动步骤

### 1. 启动后端服务

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate

# 配置数据库（编辑 .env 文件）
# 然后运行迁移
php artisan migrate

# 启动服务
php artisan serve
```

后端服务将在 `http://localhost:8000` 运行

### 2. 启动前端服务

```bash
cd frontend
npm install
npm run dev
```

前端服务将在 `http://localhost:5173` 运行

### 3. 访问文档查看器

打开浏览器访问：`http://localhost:5173/docs`

## 常见错误

### 错误：`ECONNREFUSED` 或 `http proxy error`

**原因**：后端服务未启动或无法连接

**解决方案**：
1. 检查后端服务是否在 `http://localhost:8000` 运行
2. 在浏览器中直接访问 `http://localhost:8000/api/docs` 测试后端是否正常
3. 确保防火墙没有阻止连接

### 错误：`MODULE_NOT_FOUND`

**原因**：依赖未安装

**解决方案**：
```bash
cd frontend
rm -rf node_modules package-lock.json
npm install
```

### 错误：数据库连接失败

**原因**：数据库配置错误或数据库服务未启动

**解决方案**：
1. 检查 `.env` 文件中的数据库配置
2. 确保 MySQL/PostgreSQL 服务正在运行
3. 确保数据库已创建

## 验证服务状态

### 检查后端
```bash
curl http://localhost:8000/api/docs
```

应该返回 JSON 格式的文档列表

### 检查前端
打开浏览器访问 `http://localhost:5173`，应该能看到登录页面
