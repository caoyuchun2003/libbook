# 华腾教育 - 图书管理系统

一个基于 Laravel + Vue.js 的现代化图书管理系统。

## 技术栈

- **后端**: Laravel (PHP)
- **前端**: Vue.js 3 + Vite
- **数据库**: MySQL/PostgreSQL
- **认证**: Laravel Sanctum

## 项目结构

```
libbook/
├── backend/          # Laravel 后端 API
├── frontend/         # Vue.js 前端应用
└── docs/             # 产品文档
```

## 快速开始

### 后端设置

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

### 前端设置

```bash
cd frontend
npm install
npm run dev
```

## 文档

详细文档请查看 `docs/` 目录：

- [产品需求文档](docs/PRD.md)
- [设计文档](docs/DESIGN.md)
- [API 接口文档](docs/API.md)
- [用户使用指南](docs/USER_GUIDE.md)
- [GitHub Pages 部署](DEPLOY_PAGES.md)

## 部署方式

| 组件 | 目标 |
|------|------|
| 前端 | GitHub Pages（`libbook.yuchuntest.com`） |
| API | 百度云 `/libbook/api` + CFC HTTPS 代理 |

## 开发

### 后端开发

后端 API 运行在 `http://localhost:8000`

### 前端开发

前端应用运行在 `http://localhost:5173`

## 许可证

MIT
