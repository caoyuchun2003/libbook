<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;

class ImportBookCovers extends Command
{
    protected $signature = 'books:import-covers 
                            {--urls= : 封面 URL 列表，用逗号分隔}
                            {--file= : 从文件读取封面 URL（每行一个）}
                            {--force : 强制更新所有图书的封面}';
    protected $description = '批量导入图书封面 URL';

    public function handle()
    {
        $coverUrls = [];

        // 从命令行参数获取
        if ($this->option('urls')) {
            $urls = explode(',', $this->option('urls'));
            foreach ($urls as $url) {
                $url = trim($url);
                if ($this->isValidUrl($url)) {
                    $coverUrls[] = $url;
                }
            }
        }

        // 从文件读取
        if ($this->option('file')) {
            $filePath = $this->option('file');
            if (!file_exists($filePath)) {
                $this->error("文件不存在: {$filePath}");
                return 1;
            }

            $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $url = trim($line);
                if ($this->isValidUrl($url)) {
                    $coverUrls[] = $url;
                }
            }
        }

        if (empty($coverUrls)) {
            $this->error("未提供任何封面 URL");
            $this->info("使用方法：");
            $this->info("  php artisan books:import-covers --urls=\"url1,url2,url3\"");
            $this->info("  php artisan books:import-covers --file=path/to/urls.txt");
            return 1;
        }

        $this->info("找到 " . count($coverUrls) . " 个封面 URL");

        // 获取需要更新的图书
        if ($this->option('force')) {
            $books = Book::all();
            $this->info("强制模式：将更新所有 {$books->count()} 本图书的封面");
        } else {
            $books = Book::where(function($query) {
                $query->whereNull('cover')
                      ->orWhere('cover', '');
            })->get();
        }

        if ($books->isEmpty()) {
            $this->info("没有需要更新封面的图书");
            return 0;
        }

        $this->info("找到 {$books->count()} 本需要更新封面的图书");

        // 确认操作
        if (!$this->confirm("确定要更新这些图书的封面吗？", true)) {
            $this->info("操作已取消");
            return 0;
        }

        $updated = 0;
        foreach ($books as $index => $book) {
            $coverUrl = $coverUrls[$index % count($coverUrls)];
            $book->cover = $coverUrl;
            $book->save();
            $this->info("已更新: {$book->title} - {$coverUrl}");
            $updated++;
        }

        $this->info("成功更新 {$updated} 本图书的封面");
        return 0;
    }

    /**
     * 验证 URL 是否有效
     */
    private function isValidUrl($url)
    {
        if (empty($url)) {
            return false;
        }

        // 检查是否是有效的 URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // 检查是否是图片 URL
        if (!preg_match('/\.(jpg|jpeg|png|gif|webp)(\?|$)/i', $url)) {
            return false;
        }

        return true;
    }
}
