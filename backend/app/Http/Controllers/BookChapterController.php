<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookChapter;
use App\Http\Resources\BookChapterResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BookChapterController extends Controller
{
    /**
     * 获取图书的所有章节
     */
    public function index(Book $book): JsonResponse
    {
        // 获取所有章节（扁平列表），前端会构建树形结构
        $chapters = $book->allChapters()->orderBy('order')->get();
        return BookChapterResource::collection($chapters)->response();
    }

    /**
     * 创建章节
     */
    public function store(Request $request, Book $book): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'nullable|integer|min:0',
            'parent_id' => 'nullable|exists:book_chapters,id',
            'level' => 'nullable|integer|min:1|max:5',
            'content' => 'nullable|string',
        ]);

        $validated['book_id'] = $book->id;
        $validated['order'] = $validated['order'] ?? $book->allChapters()->max('order') + 1;
        $validated['level'] = $validated['level'] ?? 1;

        $chapter = BookChapter::create($validated);

        return (new BookChapterResource($chapter->load('children')))->response()->setStatusCode(201);
    }

    /**
     * 更新章节
     */
    public function update(Request $request, BookChapter $chapter): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'order' => 'sometimes|integer|min:0',
            'parent_id' => 'sometimes|nullable|exists:book_chapters,id',
            'level' => 'sometimes|integer|min:1|max:5',
            'content' => 'nullable|string',
        ]);

        $chapter->update($validated);

        return (new BookChapterResource($chapter->load('children')))->response();
    }

    /**
     * 删除章节
     */
    public function destroy(BookChapter $chapter): JsonResponse
    {
        // 删除章节及其所有子章节
        $chapter->delete();
        
        return response()->json(['message' => '章节已删除']);
    }

    /**
     * 批量更新章节顺序
     */
    public function updateOrder(Request $request, Book $book): JsonResponse
    {
        $validated = $request->validate([
            'chapters' => 'required|array',
            'chapters.*.id' => 'required|exists:book_chapters,id',
            'chapters.*.order' => 'required|integer|min:0',
        ]);

        foreach ($validated['chapters'] as $chapterData) {
            BookChapter::where('id', $chapterData['id'])
                ->where('book_id', $book->id)
                ->update(['order' => $chapterData['order']]);
        }

        return response()->json(['message' => '顺序已更新']);
    }
}
