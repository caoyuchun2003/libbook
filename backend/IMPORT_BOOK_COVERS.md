# 图书封面说明

宇春书城**不再使用**新思课堂、豆瓣等第三方图书封面，以避免版权风险。

## 当前方案

- 未设置自有封面时，API 会按书名/作者生成原创 SVG 占位封面
- 清理历史外链：

```bash
php artisan books:sanitize-covers
# 或清空全部已存封面
php artisan books:sanitize-covers --force
```

## 自定义封面

后台编辑图书时，可填写**自有**封面图片 URL（需确保你拥有使用权）。
