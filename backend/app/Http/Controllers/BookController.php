<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Resources\BookResource;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with('category');

        // 搜索
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        // 分类筛选
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 分页
        $perPage = $request->get('per_page', 15);
        $books = $query->paginate($perPage);

        return BookResource::collection($books);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books,isbn',
            'description' => 'nullable|string',
            'content_intro' => 'nullable|string',
            'author_intro' => 'nullable|string',
            'cover' => 'nullable|string|url',
            'category_id' => 'required|exists:categories,id',
            'total_copies' => 'required|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'published_at' => 'nullable|date',
        ]);

        $validated['available_copies'] = $validated['total_copies'];

        $book = Book::create($validated);

        return new BookResource($book->load('category'));
    }

    public function show(Book $book)
    {
        return new BookResource($book->load(['category', 'chapters.children']));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'author' => 'sometimes|required|string|max:255',
            'isbn' => 'sometimes|required|string|unique:books,isbn,' . $book->id,
            'description' => 'nullable|string',
            'content_intro' => 'nullable|string',
            'author_intro' => 'nullable|string',
            'cover' => 'nullable|string|url',
            'category_id' => 'sometimes|required|exists:categories,id',
            'total_copies' => 'sometimes|required|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'published_at' => 'nullable|date',
        ]);

        $book->update($validated);

        return new BookResource($book->load('category'));
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return response()->json(['message' => '图书已删除']);
    }

    public function search($keyword)
    {
        $books = Book::with('category')
            ->where('title', 'like', "%{$keyword}%")
            ->orWhere('author', 'like', "%{$keyword}%")
            ->orWhere('isbn', 'like', "%{$keyword}%")
            ->get();

        return BookResource::collection($books);
    }
}
