<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('book_chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->string('title'); // 章节标题
            $table->integer('order')->default(0); // 排序顺序
            $table->integer('parent_id')->nullable(); // 父章节ID，支持多级目录
            $table->integer('level')->default(1); // 层级：1=一级目录，2=二级目录，3=三级目录等
            $table->text('content')->nullable(); // 章节正文内容（富文本）
            $table->timestamps();
            
            // 索引
            $table->index(['book_id', 'order']);
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_chapters');
    }
};
