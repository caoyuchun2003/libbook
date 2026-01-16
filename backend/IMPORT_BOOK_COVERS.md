# 导入图书封面指南

## 方法 1：使用命令行参数（推荐）

直接提供封面 URL 列表：

```bash
cd backend
php artisan books:import-covers --urls="https://dev.xinsiketang.com/upload/books/images/bd11effeaeab835faf36cd64f780564c.png,https://dev.xinsiketang.com/upload/books/images/example2.png"
```

## 方法 2：从文件导入

1. 创建文本文件，每行一个封面 URL：

```txt
https://dev.xinsiketang.com/upload/books/images/bd11effeaeab835faf36cd64f780564c.png
https://dev.xinsiketang.com/upload/books/images/example1.png
https://dev.xinsiketang.com/upload/books/images/example2.png
```

2. 运行命令：

```bash
cd backend
php artisan books:import-covers --file=book-covers.txt
```

## 方法 3：强制更新所有图书

如果所有图书都需要更新封面（包括已有封面的）：

```bash
php artisan books:import-covers --urls="url1,url2,url3" --force
```

## 从新思课堂网站获取封面 URL

由于网站可能使用 JavaScript 动态加载，建议：

1. 在浏览器中打开：https://www.xinsiketang.com/html/digital/textbooks
2. 打开开发者工具（F12）
3. 切换到 Network（网络）标签
4. 刷新页面
5. 筛选图片请求（Img）
6. 找到格式为 `/upload/books/images/[hash].png` 的图片
7. 复制完整的 URL（如：`https://dev.xinsiketang.com/upload/books/images/bd11effeaeab835faf36cd64f780564c.png`）
8. 将 URL 保存到文件或直接使用命令行参数导入

## 示例

```bash
# 从示例文件导入
php artisan books:import-covers --file=book-covers-example.txt

# 直接提供 URL
php artisan books:import-covers --urls="https://dev.xinsiketang.com/upload/books/images/bd11effeaeab835faf36cd64f780564c.png"
```

## 注意事项

- 封面 URL 必须是有效的图片链接
- 支持 jpg、jpeg、png、gif、webp 格式
- 如果提供的 URL 数量少于图书数量，会循环使用
- 使用 `--force` 选项会更新所有图书，包括已有封面的
