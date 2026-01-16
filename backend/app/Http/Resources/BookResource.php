<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\BookChapterResource;

class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // 默认封面URL
        $defaultCover = 'https://www.xinsiketang.com/upload/books/images/c41b38efa3492d3f1bce5873438829a1.jpeg';
        
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'description' => $this->description,
            'content_intro' => $this->content_intro,
            'author_intro' => $this->author_intro,
            'chapters' => $this->whenLoaded('chapters', function () {
                return BookChapterResource::collection($this->chapters);
            }),
            'cover' => $this->cover ?: $defaultCover,
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ],
            'total_copies' => $this->total_copies,
            'available_copies' => $this->available_copies,
            'price' => $this->price ? number_format($this->price, 2) : null,
            'published_at' => $this->published_at?->format('Y-m-d'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
