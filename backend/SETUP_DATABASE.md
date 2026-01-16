# 数据库设置指南

## 当前配置

根据 `.env` 文件，数据库配置为：
- **类型**: MySQL
- **主机**: 127.0.0.1
- **端口**: 3306
- **数据库名**: libbook
- **用户名**: root
- **密码**: (空)

## 设置步骤

### 1. 创建数据库

#### 使用 MySQL 命令行

```bash
mysql -u root -p

# 在 MySQL 中执行
CREATE DATABASE libbook CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

#### 使用 phpMyAdmin 或其他工具

1. 打开 phpMyAdmin 或 MySQL 管理工具
2. 创建新数据库，名称：`libbook`
3. 字符集选择：`utf8mb4`
4. 排序规则选择：`utf8mb4_unicode_ci`

### 2. 配置数据库连接

编辑 `backend/.env` 文件，确保数据库配置正确：

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=libbook
DB_USERNAME=root
DB_PASSWORD=your_password  # 如果有密码，填写密码
```

### 3. 测试数据库连接

```bash
cd backend
php artisan migrate:status
```

如果看到迁移列表或"Nothing to migrate"，说明连接成功。

### 4. 运行数据库迁移

```bash
cd backend
php artisan migrate
```

这将创建以下数据表：
- `users` - 用户表
- `categories` - 图书分类表
- `books` - 图书表

### 5. （可选）填充测试数据

```bash
php artisan db:seed
```

这将创建：
- 管理员账号：admin@libbook.com / admin123
- 普通用户：user@libbook.com / user123
- 示例分类和图书数据

## 使用 SQLite（开发环境，无需安装 MySQL）

如果想快速开始，可以使用 SQLite：

### 1. 修改 `.env` 文件

```env
DB_CONNECTION=sqlite
# 注释掉其他 DB_ 配置
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=libbook
# DB_USERNAME=root
# DB_PASSWORD=
```

### 2. 创建数据库文件

```bash
cd backend
touch database/database.sqlite
```

### 3. 运行迁移

```bash
php artisan migrate
```

## 验证数据库

### 检查表是否创建

```bash
php artisan migrate:status
```

### 查看数据库内容（使用 tinker）

```bash
php artisan tinker

# 在 tinker 中
DB::table('users')->count();
DB::table('books')->count();
exit
```

## 常见问题

### 问题：连接被拒绝

**原因**：MySQL 服务未启动

**解决方案**：
```bash
# macOS (使用 Homebrew)
brew services start mysql

# 或启动 MySQL 服务
sudo /usr/local/mysql/support-files/mysql.server start
```

### 问题：数据库不存在

**解决方案**：按照步骤 1 创建数据库

### 问题：权限错误

**解决方案**：确保数据库用户有创建表的权限

```sql
GRANT ALL PRIVILEGES ON libbook.* TO 'root'@'localhost';
FLUSH PRIVILEGES;
```

### 问题：字符集错误

**解决方案**：确保数据库使用 utf8mb4 字符集

```sql
ALTER DATABASE libbook CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## 重置数据库

如果需要清空并重新创建所有表：

```bash
php artisan migrate:fresh
php artisan db:seed
```

**警告**：这将删除所有现有数据！
