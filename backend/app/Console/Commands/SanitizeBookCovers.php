<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Support\BookCover;
use Illuminate\Console\Command;

class SanitizeBookCovers extends Command
{
    protected $signature = 'books:sanitize-covers {--force : Also clear every stored cover URL}';
    protected $description = '清除有版权风险的外链封面（新思课堂、豆瓣等），改由系统生成占位封面';

    public function handle(): int
    {
        $query = Book::query()->whereNotNull('cover')->where('cover', '!=', '');

        if (!$this->option('force')) {
            $query->where(function ($q) {
                $q->where('cover', 'like', '%xinsiketang.com%')
                    ->orWhere('cover', 'like', '%doubanio.com%')
                    ->orWhere('cover', 'like', '%douban.com%');
            });
        }

        $books = $query->get();

        if ($books->isEmpty()) {
            $this->info('没有需要清理的封面。');
            return self::SUCCESS;
        }

        $updated = 0;
        foreach ($books as $book) {
            if ($this->option('force') || BookCover::isBlocked($book->cover)) {
                $book->cover = null;
                $book->save();
                $updated++;
                $this->line("已清理: {$book->title}");
            }
        }

        $this->info("成功清理 {$updated} 本图书的外链封面；接口将返回自生成占位封面。");
        return self::SUCCESS;
    }
}
