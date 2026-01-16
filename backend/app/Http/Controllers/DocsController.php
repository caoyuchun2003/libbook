<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DocsController extends Controller
{
    public function getDoc($filename)
    {
        $docsPath = base_path('docs');
        $filePath = $docsPath . '/' . $filename;

        // 安全检查：防止路径遍历
        $realPath = realpath($filePath);
        $realDocsPath = realpath($docsPath);
        
        if (!$realPath || strpos($realPath, $realDocsPath) !== 0) {
            return response()->json(['error' => '文件不存在'], 404);
        }

        // 只允许 .md 文件
        if (!str_ends_with($filename, '.md')) {
            return response()->json(['error' => '不支持的文件类型'], 400);
        }

        if (!File::exists($filePath)) {
            return response()->json(['error' => '文件不存在'], 404);
        }

        $content = File::get($filePath);

        return response()->json([
            'filename' => $filename,
            'content' => $content,
        ]);
    }

    public function listDocs()
    {
        $docsPath = base_path('docs');
        
        if (!File::exists($docsPath)) {
            return response()->json(['docs' => []]);
        }

        $files = File::files($docsPath);
        $docs = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'md') {
                $docs[] = [
                    'name' => $file->getFilename(),
                    'title' => $this->getDocTitle($file->getPathname()),
                ];
            }
        }

        return response()->json(['docs' => $docs]);
    }

    private function getDocTitle($filePath)
    {
        $content = File::get($filePath);
        // 提取第一个 # 标题作为文档标题
        if (preg_match('/^#\s+(.+)$/m', $content, $matches)) {
            return trim($matches[1]);
        }
        return pathinfo($filePath, PATHINFO_FILENAME);
    }
}
