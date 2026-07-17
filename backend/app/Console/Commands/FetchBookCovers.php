<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FetchBookCovers extends Command
{
    protected $signature = 'books:fetch-covers 
                            {--url= : 已停用}
                            {--manual : 已停用}';
    protected $description = '已停用：不再从第三方站点抓取封面（版权风险）';

    public function handle(): int
    {
        $this->error('该命令已停用，避免使用第三方图书封面。');
        $this->info('请改用: php artisan books:sanitize-covers');
        $this->info('系统会为每本书生成原创 SVG 占位封面。');
        return self::FAILURE;
    }
}
