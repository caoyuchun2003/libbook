<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateBookCoversFromXinsiketangDirect extends Command
{
    protected $signature = 'books:update-covers-xinsiketang-direct 
                            {--url= : 已停用}
                            {--base-url= : 已停用}
                            {--force : 已停用}';
    protected $description = '已停用：不再从新思课堂抓取封面（版权风险）';

    public function handle(): int
    {
        $this->error('该命令已停用，避免使用新思课堂图书封面。');
        $this->info('请改用: php artisan books:sanitize-covers');
        return self::FAILURE;
    }
}
