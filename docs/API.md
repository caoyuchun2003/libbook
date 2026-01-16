# API 接口文档

## 基础信息

- **Base URL**: `http://localhost:8000/api`
- **认证方式**: Bearer Token (Laravel Sanctum)
- **Content-Type**: `application/json`
- **Accept**: `application/json`

## 认证

大部分 API 需要认证，需要在请求头中携带 Token：

```
Authorization: Bearer {token}
```

Token 通过登录接口获取，存储在 localStorage 中。

## 错误码

| 状态码 | 说明 |
|--------|------|
| 200 | 请求成功 |
| 201 | 创建成功 |
| 400 | 请求参数错误 |
| 401 | 未认证或 Token 无效 |
| 403 | 无权限 |
| 404 | 资源不存在 |
| 422 | 验证失败 |
| 500 | 服务器错误 |

## API 端点

### 1. 认证相关

#### 1.1 用户注册

**POST** `/register`

**请求体**:
```json
{
  "name": "张三",
  "email": "zhangsan@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**响应** (201):
```json
{
  "user": {
    "id": 1,
    "name": "张三",
    "email": "zhangsan@example.com",
    "role": "user"
  },
  "token": "1|xxxxxxxxxxxx",
  "token_type": "Bearer"
}
```

#### 1.2 用户登录

**POST** `/login`

**请求体**:
```json
{
  "email": "zhangsan@example.com",
  "password": "password123"
}
```

**响应** (200):
```json
{
  "user": {
    "id": 1,
    "name": "张三",
    "email": "zhangsan@example.com",
    "role": "user"
  },
  "token": "1|xxxxxxxxxxxx",
  "token_type": "Bearer"
}
```

#### 1.3 获取当前用户

**GET** `/user`

**需要认证**: 是

**响应** (200):
```json
{
  "id": 1,
  "name": "张三",
  "email": "zhangsan@example.com",
  "role": "user"
}
```

#### 1.4 用户登出

**POST** `/logout`

**需要认证**: 是

**响应** (200):
```json
{
  "message": "已成功登出"
}
```

### 2. 图书管理

#### 2.1 获取图书列表

**GET** `/books`

**需要认证**: 是

**查询参数**:
- `page` (int): 页码，默认 1
- `per_page` (int): 每页数量，默认 15
- `search` (string): 搜索关键词（标题、作者、ISBN）
- `category_id` (int): 分类ID筛选

**响应** (200):
```json
{
  "data": [
    {
      "id": 1,
      "title": "Vue.js 实战",
      "author": "张三",
      "isbn": "978-7-111-12345-6",
      "description": "Vue.js 开发指南",
      "category": {
        "id": 1,
        "name": "计算机"
      },
      "total_copies": 10,
      "available_copies": 8,
      "published_at": "2023-01-01",
      "created_at": "2024-01-01 10:00:00",
      "updated_at": "2024-01-01 10:00:00"
    }
  ],
  "current_page": 1,
  "per_page": 15,
  "total": 100,
  "last_page": 7
}
```

#### 2.2 获取图书详情

**GET** `/books/{id}`

**需要认证**: 是

**响应** (200):
```json
{
  "id": 1,
  "title": "Vue.js 实战",
  "author": "张三",
  "isbn": "978-7-111-12345-6",
  "description": "Vue.js 开发指南",
  "content_intro": "<p>内容简介...</p>",
  "author_intro": "<p>作者简介...</p>",
  "cover": "https://example.com/cover.jpg",
  "price": "99.00",
  "category": {
    "id": 1,
    "name": "计算机"
  },
  "chapters": [
    {
      "id": 1,
      "title": "第一章 入门",
      "order": 1,
      "parent_id": null,
      "level": 1,
      "content": "<p>章节内容...</p>",
      "children": [
        {
          "id": 2,
          "title": "1.1 基础概念",
          "order": 1,
          "parent_id": 1,
          "level": 2,
          "content": "<p>子章节内容...</p>",
          "children": []
        }
      ]
    }
  ],
  "total_copies": 10,
  "available_copies": 8,
  "published_at": "2023-01-01",
  "created_at": "2024-01-01 10:00:00",
  "updated_at": "2024-01-01 10:00:00"
}
```

#### 2.3 创建图书

**POST** `/books`

**需要认证**: 是

**请求体**:
```json
{
  "title": "Vue.js 实战",
  "author": "张三",
  "isbn": "978-7-111-12345-6",
  "description": "Vue.js 开发指南",
  "content_intro": "<p>内容简介...</p>",
  "author_intro": "<p>作者简介...</p>",
  "cover": "https://example.com/cover.jpg",
  "price": 99.00,
  "category_id": 1,
  "total_copies": 10,
  "published_at": "2023-01-01"
}
```

**响应** (201):
```json
{
  "id": 1,
  "title": "Vue.js 实战",
  "author": "张三",
  "isbn": "978-7-111-12345-6",
  "description": "Vue.js 开发指南",
  "category": {
    "id": 1,
    "name": "计算机"
  },
  "total_copies": 10,
  "available_copies": 10,
  "published_at": "2023-01-01",
  "created_at": "2024-01-01 10:00:00",
  "updated_at": "2024-01-01 10:00:00"
}
```

#### 2.4 更新图书

**PUT** `/books/{id}`

**需要认证**: 是

**请求体** (所有字段可选):
```json
{
  "title": "Vue.js 实战（第二版）",
  "author": "张三",
  "isbn": "978-7-111-12345-7",
  "description": "Vue.js 开发指南（更新版）",
  "category_id": 1,
  "total_copies": 15,
  "published_at": "2024-01-01"
}
```

**响应** (200): 同创建图书响应

#### 2.5 删除图书

**DELETE** `/books/{id}`

**需要认证**: 是

**响应** (200):
```json
{
  "message": "图书已删除"
}
```

#### 2.6 搜索图书

**GET** `/books/search/{keyword}`

**需要认证**: 是

**响应** (200):
```json
{
  "data": [
    {
      "id": 1,
      "title": "Vue.js 实战",
      "author": "张三",
      "isbn": "978-7-111-12345-6",
      "cover": "https://example.com/cover.jpg",
      "price": "99.00",
      "category": {
        "id": 1,
        "name": "计算机"
      },
      "total_copies": 10,
      "available_copies": 8
    }
  ]
}
```

### 3. 图书章节管理

#### 3.1 获取图书章节列表

**GET** `/books/{bookId}/chapters`

**需要认证**: 是

**响应** (200):
```json
{
  "data": [
    {
      "id": 1,
      "book_id": 1,
      "title": "第一章 入门",
      "order": 1,
      "parent_id": null,
      "level": 1,
      "content": "<p>章节正文内容...</p>",
      "children": [
        {
          "id": 2,
          "book_id": 1,
          "title": "1.1 基础概念",
          "order": 1,
          "parent_id": 1,
          "level": 2,
          "content": "<p>子章节内容...</p>",
          "children": []
        }
      ],
      "created_at": "2024-01-01 10:00:00",
      "updated_at": "2024-01-01 10:00:00"
    }
  ]
}
```

#### 3.2 创建章节

**POST** `/books/{bookId}/chapters`

**需要认证**: 是

**请求体**:
```json
{
  "title": "第一章 入门",
  "order": 1,
  "parent_id": null,
  "level": 1,
  "content": "<p>章节正文内容...</p>"
}
```

**响应** (201): 同章节列表单个对象

#### 3.3 更新章节

**PUT** `/chapters/{chapterId}`

**需要认证**: 是

**请求体** (所有字段可选):
```json
{
  "title": "第一章 入门（修订版）",
  "order": 1,
  "parent_id": null,
  "level": 1,
  "content": "<p>更新后的章节内容...</p>"
}
```

**响应** (200): 同章节列表单个对象

#### 3.4 删除章节

**DELETE** `/chapters/{chapterId}`

**需要认证**: 是

**响应** (200):
```json
{
  "message": "章节已删除"
}
```

**注意**: 删除章节会级联删除其所有子章节。

#### 3.5 批量更新章节顺序

**PUT** `/books/{bookId}/chapters/order`

**需要认证**: 是

**请求体**:
```json
{
  "chapters": [
    {
      "id": 1,
      "order": 1
    },
    {
      "id": 2,
      "order": 2
    }
  ]
}
```

**响应** (200):
```json
{
  "message": "顺序已更新"
}
```

### 4. 分类管理

#### 4.1 获取分类列表

**GET** `/categories`

**需要认证**: 是

**响应** (200):
```json
[
  {
    "id": 1,
    "name": "计算机",
    "description": "计算机相关书籍",
    "created_at": "2024-01-01 10:00:00",
    "updated_at": "2024-01-01 10:00:00"
  }
]
```

#### 4.2 获取分类详情

**GET** `/categories/{id}`

**需要认证**: 是

**响应** (200): 同分类列表单个对象

#### 4.3 创建分类

**POST** `/categories`

**需要认证**: 是

**请求体**:
```json
{
  "name": "文学",
  "description": "文学作品"
}
```

**响应** (201): 同分类列表单个对象

#### 4.4 更新分类

**PUT** `/categories/{id}`

**需要认证**: 是

**请求体**:
```json
{
  "name": "现代文学",
  "description": "现代文学作品"
}
```

**响应** (200): 同分类列表单个对象

#### 4.5 删除分类

**DELETE** `/categories/{id}`

**需要认证**: 是

**响应** (200):
```json
{
  "message": "分类已删除"
}
```

## 错误响应示例

### 验证错误 (422)

```json
{
  "message": "验证失败",
  "errors": {
    "email": [
      "邮箱格式不正确"
    ],
    "password": [
      "密码长度至少为8位"
    ]
  }
}
```

### 认证错误 (401)

```json
{
  "message": "未认证"
}
```

### 资源不存在 (404)

```json
{
  "message": "资源不存在"
}
```

## 使用示例

### JavaScript (Axios)

```javascript
import axios from 'axios'

// 配置 base URL
const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

// 登录
const login = async (email, password) => {
  const response = await api.post('/login', { email, password })
  const token = response.data.token
  
  // 存储 token
  localStorage.setItem('token', token)
  
  // 设置后续请求的认证头
  api.defaults.headers.common['Authorization'] = `Bearer ${token}`
  
  return response.data
}

// 获取图书列表
const getBooks = async () => {
  const response = await api.get('/books', {
    params: {
      page: 1,
      per_page: 15,
      search: 'Vue',
    },
  })
  return response.data
}
```

### cURL 示例

```bash
# 登录
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password123"}'

# 获取图书列表（需要认证）
curl -X GET http://localhost:8000/api/books \
  -H "Authorization: Bearer {token}"
```
