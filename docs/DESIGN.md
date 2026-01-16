# 设计文档

## 1. 系统架构设计

### 1.1 整体架构

华腾教育 采用前后端分离架构：

```
┌─────────────┐
│   Vue.js    │  前端应用 (Port 5173)
│   Frontend  │
└──────┬──────┘
       │ HTTP/HTTPS
       │ RESTful API
┌──────▼──────┐
│   Laravel   │  后端 API (Port 8000)
│   Backend   │
└──────┬──────┘
       │
┌──────▼──────┐
│   MySQL/    │  数据库
│ PostgreSQL  │
└─────────────┘
```

### 1.2 技术栈

**后端**
- Laravel 10.x
- Laravel Sanctum (API 认证)
- MySQL/PostgreSQL

**前端**
- Vue.js 3 (Composition API)
- Vue Router 4
- Pinia (状态管理)
- Axios (HTTP 客户端)
- Element Plus (UI 组件库)
- Vite (构建工具)

## 2. 数据库设计

### 2.1 ER 图

```
┌──────────┐         ┌──────────┐         ┌──────────┐
│  Users   │         │  Books   │         │Categories│
├──────────┤         ├──────────┤         ├──────────┤
│ id (PK)  │         │ id (PK)  │         │ id (PK)  │
│ name     │         │ title    │         │ name     │
│ email    │         │ category │────────►│ desc     │
│ password │         │ _id (FK) │         └──────────┘
│ role     │         │ author   │
└──────────┘         │ isbn     │
                     │ cover    │         ┌──────────────┐
                     │ price    │         │BookChapters  │
                     │ total_   │         ├──────────────┤
                     │ copies   │◄────────│ id (PK)      │
                     │ available│         │ book_id (FK) │
                     │ _copies  │         │ title        │
                     └──────────┘         │ order        │
                                          │ parent_id    │
                                          │ level        │
                                          │ content      │
                                          └──────────────┘
```

### 2.2 数据表结构

#### users 表
| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint | 主键 |
| name | varchar(255) | 姓名 |
| email | varchar(255) | 邮箱（唯一） |
| password | varchar(255) | 密码（加密） |
| role | enum('user','admin') | 角色 |
| created_at | timestamp | 创建时间 |
| updated_at | timestamp | 更新时间 |

#### categories 表
| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint | 主键 |
| name | varchar(255) | 分类名称（唯一） |
| description | text | 描述 |
| created_at | timestamp | 创建时间 |
| updated_at | timestamp | 更新时间 |

#### books 表
| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint | 主键 |
| title | varchar(255) | 标题 |
| author | varchar(255) | 作者 |
| isbn | varchar(255) | ISBN（唯一） |
| description | text | 描述 |
| content_intro | text | 内容简介（富文本） |
| author_intro | text | 作者简介（富文本） |
| cover | varchar(255) | 封面图片URL |
| price | decimal(10,2) | 售价 |
| category_id | bigint | 分类ID（外键） |
| total_copies | int | 总数量 |
| available_copies | int | 可借数量 |
| published_at | date | 出版日期 |
| created_at | timestamp | 创建时间 |
| updated_at | timestamp | 更新时间 |

#### book_chapters 表
| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint | 主键 |
| book_id | bigint | 图书ID（外键） |
| title | varchar(255) | 章节标题 |
| order | int | 排序顺序 |
| parent_id | bigint | 父章节ID（外键，支持多级） |
| level | int | 层级（1=一级，2=二级等） |
| content | text | 章节正文内容（富文本） |
| created_at | timestamp | 创建时间 |
| updated_at | timestamp | 更新时间 |

### 2.3 关系说明

- **Category ↔ Book**: 一对多
- **Book ↔ BookChapter**: 一对多
- **BookChapter ↔ BookChapter**: 自关联（父子章节关系）

## 3. API 设计规范

### 3.1 RESTful 设计原则

- 使用 HTTP 方法表示操作：GET（查询）、POST（创建）、PUT（更新）、DELETE（删除）
- 使用名词表示资源，复数形式
- 使用状态码表示结果：200（成功）、201（创建成功）、400（请求错误）、401（未认证）、404（未找到）、500（服务器错误）

### 3.2 认证方式

使用 Laravel Sanctum 进行 Token 认证：
- 登录成功后返回 Token
- 请求头携带：`Authorization: Bearer {token}`
- Token 存储在 localStorage

### 3.3 响应格式

**成功响应**
```json
{
  "data": {...},
  "message": "操作成功"
}
```

**分页响应**
```json
{
  "data": [...],
  "current_page": 1,
  "per_page": 15,
  "total": 100,
  "last_page": 7
}
```

**错误响应**
```json
{
  "message": "错误信息",
  "errors": {
    "field": ["错误详情"]
  }
}
```

## 4. 前端页面结构

### 4.1 路由设计

```
/ (首页/仪表板)
├── /login (登录页)
├── /register (注册页)
├── /docs (文档查看)
└── / (需要认证)
    ├── /books (图书列表 - 用户端)
    ├── /books/:id (图书详情 - 用户端)
    ├── /my-bookshelf (我的书架 - 用户端)
    └── /admin (管理员路由)
        ├── /admin/dashboard (管理首页)
        ├── /admin/books (图书管理)
        ├── /admin/categories (分类管理)
        ├── /admin/users (用户管理)
        └── /admin/roles (角色管理)
```

### 4.2 组件结构

```
src/
├── components/        # 可复用组件
├── views/            # 页面组件
│   ├── Login.vue
│   ├── Register.vue
│   ├── Dashboard.vue
│   ├── Books.vue (用户端)
│   ├── BookDetail.vue (用户端)
│   ├── MyBookshelf.vue (用户端)
│   ├── DocsViewer.vue
│   └── admin/        # 管理员页面
│       ├── AdminDashboard.vue
│       ├── AdminBooks.vue
│       ├── CategoryManagement.vue
│       ├── UserManagement.vue
│       └── RoleManagement.vue
├── layouts/          # 布局组件
│   └── MainLayout.vue
├── router/           # 路由配置
├── store/           # 状态管理
│   └── auth.js
└── api/             # API 调用
    ├── axios.js
    ├── auth.js
    ├── books.js
    ├── categories.js
    ├── chapters.js
    ├── users.js
    └── roles.js
```

### 4.3 状态管理

使用 Pinia 管理全局状态：

**auth store**
- `user`: 当前用户信息
- `token`: 认证令牌
- `isAuthenticated`: 是否已认证
- `login()`: 登录方法
- `logout()`: 登出方法
- `register()`: 注册方法

## 5. UI/UX 设计说明

### 5.1 设计原则

- **简洁明了**：界面简洁，信息层次清晰
- **易于操作**：常用功能易于访问
- **反馈及时**：操作有明确的成功/失败反馈
- **响应式设计**：适配不同屏幕尺寸

### 5.2 颜色方案

- **主色**：#409EFF (Element Plus 默认蓝色)
- **成功**：#67C23A
- **警告**：#E6A23C
- **危险**：#F56C6C
- **信息**：#909399

### 5.3 主要页面布局

**登录/注册页**
- 居中卡片布局
- 渐变背景
- 表单验证提示

**主布局**
- 顶部导航栏（显示用户信息、退出按钮）
- 左侧边栏（导航菜单）
- 主内容区（路由视图）

**图书列表页**
- 搜索栏和筛选器
- 表格展示
- 分页组件
- 操作按钮（查看、删除）

## 6. 安全设计

### 6.1 认证安全

- 密码使用 bcrypt 加密
- Token 有过期时间
- 支持 Token 撤销

### 6.2 数据安全

- 使用参数绑定防止 SQL 注入
- 输入验证和过滤
- XSS 防护

### 6.3 API 安全

- CORS 配置
- 请求频率限制
- 敏感操作需要认证

## 7. 性能优化

### 7.1 前端优化

- 路由懒加载
- 组件按需加载
- API 请求防抖
- 图片懒加载（如需要）

### 7.2 后端优化

- 数据库索引优化
- 查询优化（Eager Loading）
- API 响应缓存（如需要）
- 分页查询

## 8. 部署架构

### 8.1 开发环境

- 前端：Vite Dev Server (localhost:5173)
- 后端：Laravel Artisan Serve (localhost:8000)
- 数据库：本地 MySQL/PostgreSQL

### 8.2 生产环境（建议）

- 前端：Nginx 静态文件服务
- 后端：Nginx + PHP-FPM
- 数据库：独立 MySQL/PostgreSQL 服务器
- 可选：Redis 缓存、CDN 加速
