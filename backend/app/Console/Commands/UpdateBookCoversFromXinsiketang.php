<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateBookCoversFromXinsiketang extends Command
{
    protected $signature = 'books:update-covers-xinsiketang';
    protected $description = '已停用：不再从新思课堂抓取封面（版权风险）';

    public function handle(): int
    {
        $this->error('该命令已停用，避免使用新思课堂/豆瓣图书封面。');
        $this->info('请改用: php artisan books:sanitize-covers');
        return self::FAILURE;
    }
}
