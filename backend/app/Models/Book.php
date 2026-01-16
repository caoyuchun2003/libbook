<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'description',
        'content_intro',
        'author_intro',
        'cover',
        'category_id',
        'total_copies',
        'available_copies',
        'price',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * 图书章节
     */
    public function chapters()
    {
        return $this->hasMany(BookChapter::class)->whereNull('parent_id')->orderBy('order');
    }

    /**
     * 所有章节（包括子章节）
     */
    public function allChapters()
    {
        return $this->hasMany(BookChapter::class)->orderBy('order');
    }
}
