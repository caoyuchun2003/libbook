# GitHub Pages 前端 + 百度云后端 + CFC HTTPS 代理

## 架构

```text
浏览器
  ├─ Vue 前端 → GitHub Pages → https://libbook.yuchuntest.com/
  └─ API      → CFC HTTPS → http://180.76.180.105/libbook/api/
```

| 层 | 位置 |
|----|------|
| 前端 | GitHub Pages（本仓库 Actions） |
| HTTPS 入口 | 百度 CFC（`functions/`） |
| Laravel + MySQL | 百度云 Docker（现状 `/libbook/`） |

## 一、CFC

见 [functions/README.md](./functions/README.md)

Secret：`VITE_LIBBOOK_API_BASE` = `https://xxxx.cfc-execute.bj.baidubce.com/libbook/api`

## 二、Pages

1. Settings → Pages → Source: **GitHub Actions**
2. 自定义域（可选）：DNS `libbook` CNAME → `caoyuchun2003.github.io`  
   仓库 Pages Custom domain：`libbook.yuchuntest.com`
3. 注意：私有仓库免费账户可能无法开 Pages，需改为 **public** 或 GitHub Pro

## 三、构建变量

| 变量 | Pages（自定义域） | 百度云同机 `/libbook/` |
|------|-------------------|------------------------|
| `VITE_BASE` | `/` | `/libbook/` |
| `VITE_ROUTER_BASE` | `/` | `/libbook/` |
| `VITE_API_BASE` | CFC `.../libbook/api` | `/libbook/api` |

## 四、检查清单

- [ ] CFC `/libbook/api/books` 返回 401（说明路由通）
- [ ] Secret 已设
- [ ] Pages Actions 绿
- [ ] https://libbook.yuchuntest.com 能打开登录页并调 API
