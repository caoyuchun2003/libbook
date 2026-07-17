<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;

class UpdateBookCoversFromSample extends Command
{
    protected $signature = 'books:update-covers-sample';
    protected $description = '清除外链封面，改用系统自生成占位封面（无版权风险）';

    public function handle(): int
    {
        $this->warn('已不再使用豆瓣/第三方外链封面。');
        $this->info('请运行: php artisan books:sanitize-covers --force');

        $updated = Book::query()
            ->whereNotNull('cover')
            ->where('cover', '!=', '')
            ->update(['cover' => null]);

        $this->info("已清空 {$updated} 本图书的封面字段；接口将返回自生成 SVG 占位封面。");
        return self::SUCCESS;
    }
}
