<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;

class UpdateBookCoversFromSample extends Command
{
    protected $signature = 'books:update-covers-sample';
    protected $description = '使用示例封面 URL 更新数据库中的图书封面';

    public function handle()
    {
        // 一些示例图书封面 URL（可以从新思课堂或其他图书网站获取）
        $sampleCovers = [
            'https://img3.doubanio.com/view/subject/s/public/s29905068.jpg',
            'https://img3.doubanio.com/view/subject/s/public/s29905069.jpg',
            'https://img3.doubanio.com/view/subject/s/public/s29905070.jpg',
            'https://img3.doubanio.com/view/subject/s/public/s29905071.jpg',
            'https://img3.doubanio.com/view/subject/s/public/s29905072.jpg',
            'https://img1.doubanio.com/view/subject/s/public/s29905073.jpg',
            'https://img1.doubanio.com/view/subject/s/public/s29905074.jpg',
            'https://img1.doubanio.com/view/subject/s/public/s29905075.jpg',
            'https://img1.doubanio.com/view/subject/s/public/s29905076.jpg',
            'https://img1.doubanio.com/view/subject/s/public/s29905077.jpg',
        ];

        // 获取所有没有封面的图书
        $books = Book::where(function($query) {
            $query->whereNull('cover')
                  ->orWhere('cover', '');
        })->get();

        if ($books->isEmpty()) {
            $this->info("所有图书都已设置封面");
            return 0;
        }

        $this->info("找到 {$books->count()} 本需要更新封面的图书");

        $updated = 0;
        foreach ($books as $index => $book) {
            $coverUrl = $sampleCovers[$index % count($sampleCovers)];
            $book->cover = $coverUrl;
            $book->save();
            $this->info("已更新: {$book->title} - {$coverUrl}");
            $updated++;
        }

        $this->info("成功更新 {$updated} 本图书的封面");
        return 0;
    }
}
