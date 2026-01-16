<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookChapterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,
            'book_id' => (int) $this->book_id,
            'title' => $this->title,
            'order' => (int) $this->order,
            'parent_id' => $this->parent_id ? (int) $this->parent_id : null,
            'level' => (int) $this->level,
            'content' => $this->content,
            'children' => $this->whenLoaded('children', function () {
                return BookChapterResource::collection($this->children);
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
