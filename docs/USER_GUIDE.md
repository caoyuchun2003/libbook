# 用户使用指南

## 目录

1. [安装部署](#安装部署)
2. [快速开始](#快速开始)
3. [功能使用](#功能使用)
4. [常见问题](#常见问题)
5. [故障排查](#故障排查)

## 安装部署

### 环境要求

**后端**
- PHP >= 8.1
- Composer
- MySQL >= 5.7 或 PostgreSQL >= 10
- Laravel 10.x

**前端**
- Node.js >= 16.x (推荐 20.x)
- npm 或 yarn

### 后端安装

1. **进入后端目录**
```bash
cd backend
```

2. **安装依赖**
```bash
composer install
```

3. **配置环境变量**
```bash
cp .env.example .env
php artisan key:generate
```

4. **编辑 `.env` 文件**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=libbook
DB_USERNAME=root
DB_PASSWORD=your_password

APP_URL=http://localhost:8000
SANCTUM_STATEFUL_DOMAINS=localhost:5173,127.0.0.1:5173
```

5. **运行数据库迁移**
```bash
php artisan migrate
```

6. **（可选）填充测试数据**
```bash
php artisan db:seed
```

7. **启动开发服务器**
```bash
php artisan serve
```

后端服务将在 `http://localhost:8000` 运行。

### 前端安装

1. **进入前端目录**
```bash
cd frontend
```

2. **安装依赖**
```bash
npm install
```

3. **启动开发服务器**
```bash
npm run dev
```

前端应用将在 `http://localhost:5173` 运行。

**注意**: 开发服务器默认配置为可通过 IP 地址访问，启动后终端会显示本地和网络访问地址，例如：
```
  ➜  Local:   http://localhost:5173/
  ➜  Network: http://192.168.1.100:5173/
```
您可以使用网络地址在同一局域网的其他设备上访问应用。

### 生产环境部署

#### 后端部署

1. **优化配置**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

2. **配置 Web 服务器（Nginx 示例）**
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/libbook/backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### 前端部署

1. **构建生产版本**
```bash
npm run build
```

2. **部署 dist 目录**
将 `frontend/dist` 目录部署到 Web 服务器（如 Nginx）或 CDN。

3. **配置 API 代理（Nginx 示例）**
```nginx
location /api {
    proxy_pass http://localhost:8000;
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
}
```

## 快速开始

### 1. 注册账号

1. 访问 `http://localhost:5173`
2. 点击"注册"或访问 `/register`
3. 填写信息：
   - 姓名
   - 邮箱
   - 密码（至少8位）
   - 确认密码
4. 点击"注册"按钮

### 2. 登录

1. 访问 `/login`
2. 输入邮箱和密码
3. 点击"登录"

### 3. 浏览图书

1. 登录后进入首页，可以看到数据统计
2. 点击左侧菜单"图书管理"
3. 可以：
   - 搜索图书（标题、作者、ISBN）
   - 按分类筛选
   - 查看图书详情

## 功能使用

### 图书管理

#### 搜索图书
- 在搜索框输入关键词（标题、作者、ISBN）
- 支持模糊搜索
- 可以结合分类筛选使用

#### 添加图书（管理员）
1. 点击"添加图书"按钮
2. 填写图书信息：
   - 标题（必填）
   - 作者（必填）
   - ISBN（必填，唯一）
   - 分类（必填）
   - 总数量（必填）
   - 封面（可选）：封面图片 URL
   - 售价（可选）：图书售价
   - 描述（可选）
   - 内容简介（可选）：支持 HTML 格式的富文本
   - 作者简介（可选）：支持 HTML 格式的富文本
   - 出版日期（可选）
3. 点击"确定"

#### 编辑图书（管理员）
1. 在图书列表中点击"编辑"按钮
2. 修改信息
3. 保存

#### 删除图书（管理员）
1. 在图书列表中点击"删除"按钮
2. 确认删除

### 分类管理（管理员）

1. 在图书管理页面可以管理分类
2. 添加、编辑、删除分类
3. 分类删除前需确保没有关联图书

### 章节管理（管理员）

#### 管理图书章节
1. 在图书管理页面，找到要管理的图书
2. 点击"章节"按钮，打开章节管理对话框
3. 章节以树形结构展示，支持多级章节

#### 添加章节
1. 在章节管理对话框中，点击"添加章节"按钮
2. 填写章节信息：
   - 章节标题（必填）
   - 父章节（可选）：选择父章节可创建子章节
   - 正文内容（可选）：支持 HTML 格式的富文本内容
3. 点击"确定"保存

#### 添加子章节
1. 在章节树中找到要添加子章节的父章节
2. 点击该章节的"子章节"按钮
3. 填写子章节信息（父章节已自动选择）
4. 保存

#### 编辑章节
1. 在章节树中找到要编辑的章节
2. 点击"编辑"按钮
3. 修改章节标题或正文内容
4. 保存

#### 删除章节
1. 在章节树中找到要删除的章节
2. 点击"删除"按钮
3. 确认删除
4. **注意**：删除章节会级联删除其所有子章节

### 查看图书详情（用户）

#### 图书基本信息
- 图书封面：可点击放大查看
- 标题、作者、ISBN
- 分类标签
- 售价（如有）
- 出版日期

#### 目录导航
- 目录以树形结构展示
- 点击目录项可自动滚动到对应章节
- 支持多级目录结构

#### 章节内容
- 章节标题以大标题样式显示
- 章节正文内容以富文本格式展示
- 子章节有缩进和视觉区分
- 支持 HTML 格式的内容（图片、链接、格式化文本等）

#### 内容简介和作者简介
- 内容简介：图书的详细介绍（富文本）
- 作者简介：作者的个人介绍（富文本）

## 常见问题

### Q1: 忘记密码怎么办？

目前系统不支持密码重置功能。请联系管理员重置密码。

### Q2: 如何修改个人信息？

个人信息修改功能将在后续版本中提供。

### Q3: 如何成为管理员？

管理员账号需要由系统管理员在数据库中手动设置 `role` 字段为 `admin`。

### Q4: 图书可以批量操作吗？

目前不支持批量操作，后续版本将添加此功能。

## 故障排查

### 前端问题

#### 问题：页面无法加载
**解决方案**：
1. 检查 Node.js 版本：`node --version`（需要 >= 16）
2. 重新安装依赖：`rm -rf node_modules && npm install`
3. 清除缓存：`npm run build -- --force`

#### 问题：API 请求失败
**解决方案**：
1. 检查后端服务是否运行（`http://localhost:8000`）
2. 检查 `vite.config.js` 中的代理配置
3. 检查浏览器控制台的错误信息

#### 问题：登录后跳转失败
**解决方案**：
1. 检查 localStorage 中是否有 token
2. 检查路由守卫配置
3. 清除浏览器缓存和 localStorage

### 后端问题

#### 问题：数据库连接失败
**解决方案**：
1. 检查 `.env` 文件中的数据库配置
2. 确认数据库服务正在运行
3. 测试数据库连接：`php artisan tinker` 然后执行数据库查询

#### 问题：迁移失败
**解决方案**：
1. 检查数据库是否存在
2. 检查用户权限
3. 尝试重置迁移：`php artisan migrate:fresh`

#### 问题：Token 认证失败
**解决方案**：
1. 检查 `SANCTUM_STATEFUL_DOMAINS` 配置
2. 检查 CORS 配置
3. 确认请求头包含正确的 Authorization

#### 问题：500 服务器错误
**解决方案**：
1. 查看 `storage/logs/laravel.log` 日志文件
2. 检查文件权限：`chmod -R 775 storage bootstrap/cache`
3. 清除缓存：`php artisan cache:clear`

### 数据库问题

#### 问题：表不存在
**解决方案**：
```bash
php artisan migrate
```

#### 问题：数据丢失
**解决方案**：
1. 检查数据库备份
2. 如有迁移文件，可以重新填充数据

### 性能问题

#### 问题：页面加载慢
**解决方案**：
1. 检查网络连接
2. 优化数据库查询（添加索引）
3. 启用缓存
4. 使用 CDN 加速静态资源

#### 问题：API 响应慢
**解决方案**：
1. 检查数据库查询性能
2. 添加数据库索引
3. 使用查询缓存
4. 优化 N+1 查询问题

## 技术支持

如遇到其他问题，请：
1. 查看日志文件
2. 检查文档
3. 联系技术支持团队

## 更新日志

### v1.2.0 (2024-01-09)
- 新增章节管理功能
  - 支持多级章节结构
  - 章节正文内容管理（富文本）
  - 目录自动生成
- 新增图书封面和售价字段
- 新增内容简介和作者简介（富文本）
- 优化图书详情页展示
- 移除 table_of_contents 字段，统一使用章节表管理
- 前端支持通过 IP 地址访问

### v1.1.0 (2024-01-05)
- 新增用户管理功能
- 新增角色管理功能
- 优化管理员界面

### v1.0.0 (2024-01-01)
- 初始版本发布
- 用户认证功能
- 图书管理功能
- 基础分类管理