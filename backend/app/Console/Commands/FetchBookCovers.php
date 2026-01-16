<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use DOMDocument;
use DOMXPath;

class FetchBookCovers extends Command
{
    protected $signature = 'books:fetch-covers 
                            {--url=https://www.xinsiketang.com/html/digital/textbooks}
                            {--manual : 手动输入封面 URL 列表}';
    protected $description = '从指定网页抓取图书封面 URL 并更新到数据库';

    public function handle()
    {
        if ($this->option('manual')) {
            return $this->handleManualInput();
        }

        $url = $this->option('url');
        $this->info("正在从 {$url} 抓取图书封面...");

        try {
            // 获取网页内容，添加请求头绕过反爬虫
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language' => 'zh-CN,zh;q=0.9,en;q=0.8',
                    'Referer' => 'https://www.xinsiketang.com/',
                    'Accept-Encoding' => 'gzip, deflate, br',
                ])
                ->get($url);
            
            if (!$response->successful()) {
                $this->error("无法访问网页，状态码: {$response->status()}");
                $this->warn("提示：网站可能有反爬虫机制，建议使用 --manual 选项手动输入封面 URL");
                return 1;
            }

            $html = $response->body();
            
            // 解析 HTML
            $dom = new DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
            $xpath = new DOMXPath($dom);

            // 查找所有图片标签
            $images = $xpath->query("//img[@src or @data-src or @data-original]");
            
            $coverUrls = [];
            foreach ($images as $img) {
                $src = $img->getAttribute('src') 
                    ?: $img->getAttribute('data-src') 
                    ?: $img->getAttribute('data-original')
                    ?: $img->getAttribute('data-lazy-src');
                
                // 过滤掉非封面图片（logo、图标等）
                if ($src && $this->isBookCover($src)) {
                    $fullUrl = $this->makeAbsoluteUrl($src, $url);
                    $coverUrls[] = $fullUrl;
                    $this->line("找到封面: {$fullUrl}");
                }
            }

            // 也尝试使用正则表达式直接提取新思课堂格式的 URL
            if (empty($coverUrls)) {
                preg_match_all('/https?:\/\/[^"\'\s]+\/upload\/books\/images\/[^"\'\s]+\.(jpg|jpeg|png|gif|webp)/i', $html, $matches);
                if (!empty($matches[0])) {
                    foreach ($matches[0] as $match) {
                        if (!in_array($match, $coverUrls)) {
                            $coverUrls[] = $match;
                            $this->line("找到封面（正则）: {$match}");
                        }
                    }
                }
            }

            if (empty($coverUrls)) {
                $this->warn("未找到图书封面图片");
                $this->info("尝试查找其他可能的图片元素...");
                
                // 尝试查找背景图片
                $elements = $xpath->query("//*[@style]");
                foreach ($elements as $element) {
                    $style = $element->getAttribute('style');
                    if (preg_match('/background-image:\s*url\(["\']?([^"\']+)["\']?\)/i', $style, $matches)) {
                        $fullUrl = $this->makeAbsoluteUrl($matches[1], $url);
                        if ($this->isBookCover($fullUrl)) {
                            $coverUrls[] = $fullUrl;
                            $this->line("找到封面（背景图）: {$fullUrl}");
                        }
                    }
                }
            }

            if (empty($coverUrls)) {
                $this->error("未能从网页中提取到图书封面 URL");
                $this->info("提示：网页可能使用 JavaScript 动态加载内容，需要其他方式抓取");
                return 1;
            }

            // 更新数据库中的图书
            $books = Book::whereNull('cover')->orWhere('cover', '')->get();
            
            if ($books->isEmpty()) {
                $this->warn("数据库中没有需要更新封面的图书");
                return 0;
            }

            $this->info("找到 {$books->count()} 本需要更新封面的图书");
            $this->info("找到 " . count($coverUrls) . " 个封面 URL");

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
     * 判断是否是图书封面图片
     */
    private function isBookCover($url)
    {
        $url = strtolower($url);
        
        // 排除常见的非封面图片
        $excludePatterns = [
            '/logo/i',
            '/icon/i',
            '/avatar/i',
            '/banner/i',
            '/ad/i',
            '/advertisement/i',
            '/button/i',
            '/\.(ico|svg)$/i',
        ];

        foreach ($excludePatterns as $pattern) {
            if (preg_match($pattern, $url)) {
                return false;
            }
        }

        // 新思课堂封面 URL 格式：/upload/books/images/
        if (preg_match('/\/upload\/books\/images\//i', $url)) {
            return true;
        }

        // 包含封面相关关键词
        $coverKeywords = [
            'cover',
            'book',
            'textbook',
            '教材',
            '图书',
            '封面',
            '/books/',
            '/book/',
        ];

        foreach ($coverKeywords as $keyword) {
            if (strpos($url, $keyword) !== false) {
                return true;
            }
        }

        // 如果 URL 看起来像是图片文件（jpg, png, webp等）
        if (preg_match('/\.(jpg|jpeg|png|gif|webp)(\?|$)/i', $url)) {
            // 检查图片尺寸相关的 URL 参数或路径
            // 封面图片通常会有特定的尺寸标识
            return true;
        }

        return false;
    }

    /**
     * 将相对 URL 转换为绝对 URL
     */
    private function makeAbsoluteUrl($url, $baseUrl)
    {
        // 如果已经是绝对 URL，直接返回
        if (preg_match('/^https?:\/\//', $url)) {
            return $url;
        }

        // 解析基础 URL
        $base = parse_url($baseUrl);
        $basePath = isset($base['path']) ? dirname($base['path']) : '/';
        $basePath = rtrim($basePath, '/') . '/';

        // 处理相对路径
        if (strpos($url, '/') === 0) {
            // 绝对路径
            return $base['scheme'] . '://' . $base['host'] . $url;
        } else {
            // 相对路径
            return $base['scheme'] . '://' . $base['host'] . $basePath . ltrim($url, './');
        }
    }

    /**
     * 处理手动输入封面 URL
     */
    private function handleManualInput()
    {
        $this->info("手动输入模式：请输入图书封面 URL（每行一个，输入空行结束）");
        
        $coverUrls = [];
        while (true) {
            $line = $this->ask("封面 URL（留空结束）");
            if (empty(trim($line))) {
                break;
            }
            if (filter_var($line, FILTER_VALIDATE_URL)) {
                $coverUrls[] = $line;
                $this->info("已添加: {$line}");
            } else {
                $this->warn("无效的 URL，已跳过");
            }
        }

        if (empty($coverUrls)) {
            $this->error("未输入任何有效的封面 URL");
            return 1;
        }

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
        $this->info("找到 " . count($coverUrls) . " 个封面 URL");

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
    }
}
