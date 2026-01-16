<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class UpdateBookCoversDefault extends Seeder
{
    /**
     * 为所有没有封面的书籍设置默认封面
     */
    public function run(): void
    {
        $defaultCover = 'https://www.xinsiketang.com/upload/books/images/c41b38efa3492d3f1bce5873438829a1.jpeg';
        
        $booksWithoutCover = Book::where(function($query) {
            $query->whereNull('cover')
                  ->orWhere('cover', '');
        })->get();
        
        $count = 0;
        foreach ($booksWithoutCover as $book) {
            $book->cover = $defaultCover;
            $book->save();
            $count++;
        }
        
        $this->command->info("已为 {$count} 本图书设置默认封面");
    }
}
