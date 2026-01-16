-- 删除借阅记录表的 SQL 脚本
-- 如果数据库中已经存在 borrow_records 表，运行此脚本可以删除它

-- 先删除外键约束（如果存在）
ALTER TABLE `borrow_records` DROP FOREIGN KEY IF EXISTS `borrow_records_user_id_foreign`;
ALTER TABLE `borrow_records` DROP FOREIGN KEY IF EXISTS `borrow_records_book_id_foreign`;

-- 删除表
DROP TABLE IF EXISTS `borrow_records`;
