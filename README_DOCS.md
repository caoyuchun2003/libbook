# 文档查看器使用说明

## 功能说明

项目已集成了文档查看器功能，可以在线查看项目文档。**文档查看器是纯前端实现，不需要后端服务支持！**

## 访问方式

1. **直接访问**：访问 `/docs` 路径（无需登录）
2. **已登录用户**：在左侧菜单点击"项目文档"

## 功能特性

- 📚 纯前端实现，无需后端服务
- 📖 实时渲染 Markdown 内容
- 🎨 代码高亮显示（支持多种编程语言）
- 📱 响应式设计，适配不同屏幕
- 🔍 左侧导航栏快速切换文档
- 🔗 支持 URL 参数直接访问特定文档
- 🚀 独立运行，只需启动前端服务

## 文档位置

**重要**：文档文件统一存放在项目根目录的 `docs/` 目录下，`frontend/public/docs/` 只是一个符号链接，指向根目录的 `docs/`。

这样只需要维护一份文档，修改项目根目录的 `docs/` 中的文件即可。

当前文档：
- **PRD.md** - 产品需求文档
- **DESIGN.md** - 设计文档
- **API.md** - API 接口文档
- **USER_GUIDE.md** - 用户使用指南

### 设置文档链接

如果符号链接不存在，可以运行：

**Linux/macOS:**
```bash
cd frontend
./setup-docs.sh
# 或
npm run setup-docs
```

**Windows:**
```bash
cd frontend
setup-docs.bat
# 或手动执行（需要管理员权限）:
mklink /D public\docs ..\..\docs
```

## 技术实现

### 前端（纯前端实现）
- Vue 3 + Composition API
- Marked.js 用于 Markdown 渲染
- Highlight.js 用于代码高亮
- Element Plus UI 组件
- 直接从 `public/docs/` 目录读取文件（使用 `fetch` API）

### 优势
- ✅ **无需后端**：文档查看器完全独立，只需要前端服务
- ✅ **快速加载**：静态文件，加载速度快
- ✅ **易于部署**：可以直接部署到 CDN 或静态服务器
- ✅ **零配置**：不需要数据库或 API 配置

## 添加新文档

1. 将新的 `.md` 文件复制到 `frontend/public/docs/` 目录
2. 在 `DocsViewer.vue` 的 `docList` 数组中添加文档配置（可选，用于显示友好标题）
3. 刷新页面即可看到新文档

## 样式定制

文档查看器使用 GitHub 风格的 Markdown 样式，如需自定义样式，可以修改 `DocsViewer.vue` 中的 CSS。

## 启动方式

只需要启动前端服务即可：

```bash
cd frontend
npm install
npm run dev
```

然后访问 `http://localhost:5173/docs` 即可查看文档，**完全不需要启动后端服务**！
