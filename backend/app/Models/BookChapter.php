<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookChapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'title',
        'order',
        'parent_id',
        'level',
        'content',
    ];

    /**
     * 所属图书
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * 父章节
     */
    public function parent()
    {
        return $this->belongsTo(BookChapter::class, 'parent_id');
    }

    /**
     * 子章节
     */
    public function children()
    {
        return $this->hasMany(BookChapter::class, 'parent_id')->orderBy('order');
    }

    /**
     * 获取所有子章节（递归）
     */
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }
}
