<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class UpdateBookCoversDefault extends Seeder
{
    /**
     * 清除外链封面，由 API 生成无版权占位封面
     */
    public function run(): void
    {
        $count = Book::query()
            ->where(function ($query) {
                $query->where('cover', 'like', '%xinsiketang.com%')
                    ->orWhere('cover', 'like', '%doubanio.com%')
                    ->orWhere('cover', 'like', '%douban.com%')
                    ->orWhereNull('cover')
                    ->orWhere('cover', '');
            })
            ->update(['cover' => null]);

        $this->command->info("已清理/重置 {$count} 本图书封面（使用自生成占位图）");
    }
}
