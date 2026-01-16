<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateBookCoversFromXinsiketangDirect extends Command
{
    protected $signature = 'books:update-covers-xinsiketang-direct 
                            {--url=https://www.xinsiketang.com/html/digital/textbooks}
                            {--base-url=https://dev.xinsiketang.com}';
    protected $description = '从新思课堂网站直接抓取图书封面（格式：/upload/books/images/）';

    public function handle()
    {
        $url = $this->option('url');
        $baseUrl = $this->option('base-url');
        
        $this->info("正在从 {$url} 抓取图书封面...");
        $this->info("使用基础 URL: {$baseUrl}");

        try {
            // 获取网页内容
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language' => 'zh-CN,zh;q=0.9',
                    'Referer' => 'https://www.xinsiketang.com/',
                ])
                ->get($url);

            if (!$response->successful()) {
                $this->error("无法访问网页，状态码: {$response->status()}");
                $this->warn("提示：网站可能有反爬虫机制");
                return 1;
            }

            $html = $response->body();
            
            // 使用正则表达式提取新思课堂格式的封面 URL
            // 格式：/upload/books/images/[hash].png 或完整 URL
            $coverUrls = [];
            
            // 匹配完整 URL
            preg_match_all('/https?:\/\/[^"\'\s]+\/upload\/books\/images\/[a-f0-9]+\.(jpg|jpeg|png|gif|webp)/i', $html, $fullUrlMatches);
            if (!empty($fullUrlMatches[0])) {
                foreach ($fullUrlMatches[0] as $match) {
                    if (!in_array($match, $coverUrls)) {
                        $coverUrls[] = $match;
                        $this->line("找到封面（完整URL）: {$match}");
                    }
                }
            }

            // 匹配相对路径
            preg_match_all('/["\'](\/upload\/books\/images\/[a-f0-9]+\.(jpg|jpeg|png|gif|webp))["\']/i', $html, $relativeMatches);
            if (!empty($relativeMatches[1])) {
                foreach ($relativeMatches[1] as $match) {
                    $fullUrl = rtrim($baseUrl, '/') . $match;
                    if (!in_array($fullUrl, $coverUrls)) {
                        $coverUrls[] = $fullUrl;
                        $this->line("找到封面（相对路径）: {$fullUrl}");
                    }
                }
            }

            // 也尝试从 data 属性中提取
            preg_match_all('/data-(?:src|original|lazy-src)=["\']([^"\']*\/upload\/books\/images\/[^"\']+)["\']/i', $html, $dataMatches);
            if (!empty($dataMatches[1])) {
                foreach ($dataMatches[1] as $match) {
                    $fullUrl = $this->makeAbsoluteUrl($match, $baseUrl);
                    if (!in_array($fullUrl, $coverUrls)) {
                        $coverUrls[] = $fullUrl;
                        $this->line("找到封面（data属性）: {$fullUrl}");
                    }
                }
            }

            if (empty($coverUrls)) {
                $this->error("未能从网页中提取到图书封面 URL");
                $this->info("提示：网页可能使用 JavaScript 动态加载内容");
                $this->info("建议：在浏览器中打开页面，使用开发者工具查看网络请求，找到封面图片的实际 URL");
                return 1;
            }

            // 去重
            $coverUrls = array_unique($coverUrls);
            $this->info("共找到 " . count($coverUrls) . " 个封面 URL");

            // 更新数据库中的图书
            $books = Book::where(function($query) {
                $query->whereNull('cover')
                      ->orWhere('cover', '');
            })->get();
            
            if ($books->isEmpty()) {
                $this->warn("数据库中没有需要更新封面的图书");
                $this->info("提示：使用 --force 选项可以强制更新所有图书的封面");
                return 0;
            }

            $this->info("找到 {$books->count()} 本需要更新封面的图书");

            $updated = 0;
            foreach ($books as $index => $book) {
                if (isset($coverUrls[$index % count($coverUrls)])) {
                    $book->cover = $coverUrls[$index % count($coverUrls)];
                    $book->save();
                    $this->info("已更新: {$book->title} - {$book->cover}");
                    $updated++;
                }
            }

            $this->info("成功更新 {$updated} 本图书的封面");
            return 0;

        } catch (\Exception $e) {
            $this->error("抓取失败: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * 将相对 URL 转换为绝对 URL
     */
    private function makeAbsoluteUrl($url, $baseUrl)
    {
        if (preg_match('/^https?:\/\//', $url)) {
            return $url;
        }

        $base = parse_url($baseUrl);
        if (strpos($url, '/') === 0) {
            return $base['scheme'] . '://' . $base['host'] . $url;
        } else {
            $basePath = isset($base['path']) ? dirname($base['path']) : '/';
            $basePath = rtrim($basePath, '/') . '/';
            return $base['scheme'] . '://' . $base['host'] . $basePath . ltrim($url, './');
        }
    }
}
