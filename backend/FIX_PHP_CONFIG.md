# 修复 PHP 配置问题

## 问题描述

如果遇到以下错误：
```
Fatal error: Failed opening required '/Applications/Herd.app/Contents/Resources/valet/dump-loader.php'
```

这是因为 PHP 配置中引用了不存在的文件。

## 解决方案

### 方案 1：使用系统 PHP（推荐）

```bash
# 查找可用的 PHP
which -a php

# 使用系统 PHP 启动
/usr/local/bin/php artisan serve
```

### 方案 2：修复 Herd PHP 配置

编辑 PHP 配置文件，移除对不存在文件的引用：

```bash
# 查找 PHP 配置文件
php --ini

# 编辑配置文件，注释掉或删除引用 dump-loader.php 的行
```

### 方案 3：重新安装/更新 Herd

如果使用 Herd，可以尝试：
1. 更新 Herd 到最新版本
2. 或重新安装 Herd

### 方案 4：使用 Docker（可选）

如果环境问题难以解决，可以考虑使用 Docker：

```bash
# 使用 Laravel Sail
composer require laravel/sail --dev
php artisan sail:install
./vendor/bin/sail up
```

## 临时解决方案

即使有 PHP 警告，Laravel 通常也能正常运行。可以：

1. 忽略警告，直接启动服务
2. 使用启动脚本（会自动处理）

```bash
cd backend
./start.sh
```

## 验证服务

启动后访问：
- http://localhost:8000
- http://localhost:8000/api/docs

如果看到 JSON 响应，说明服务正常运行。
