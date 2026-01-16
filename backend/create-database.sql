-- 创建 LibBook 数据库
-- 执行方法: mysql -u root -p < create-database.sql

CREATE DATABASE IF NOT EXISTS libbook 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- 显示创建结果
SHOW DATABASES LIKE 'libbook';
