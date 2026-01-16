<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Book;
use App\Models\BookChapter;

class GenerateBookChapters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'book:generate-chapters {book_id? : 图书ID，如果不提供则查找"Vue.js 实战"}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '为指定图书批量生成章节';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bookId = $this->argument('book_id');
        
        if (!$bookId) {
            // 查找 Vue.js 实战
            $book = Book::where('title', 'like', '%Vue.js%')->first();
            if (!$book) {
                $this->error('未找到"Vue.js 实战"这本书');
                return 1;
            }
            $bookId = $book->id;
            $this->info("找到图书: {$book->title} (ID: {$bookId})");
        } else {
            $book = Book::find($bookId);
            if (!$book) {
                $this->error("未找到ID为 {$bookId} 的图书");
                return 1;
            }
        }

        // Vue.js 实战的章节结构
        $chaptersData = [
            [
                'title' => '第一章 Vue.js 基础',
                'level' => 1,
                'order' => 1,
                'content' => '<p>本章介绍 Vue.js 的基础概念和核心特性。</p><h3>主要内容：</h3><ul><li>Vue.js 简介</li><li>安装和配置</li><li>第一个 Vue 应用</li><li>模板语法</li><li>指令系统</li></ul>',
                'children' => [
                    ['title' => '1.1 Vue.js 简介', 'level' => 2, 'order' => 1, 'content' => '<p>Vue.js 是一套用于构建用户界面的渐进式框架。</p>'],
                    ['title' => '1.2 安装和配置', 'level' => 2, 'order' => 2, 'content' => '<p>可以通过多种方式安装 Vue.js，包括 CDN、npm 等。</p>'],
                    ['title' => '1.3 第一个 Vue 应用', 'level' => 2, 'order' => 3, 'content' => '<p>创建一个简单的 Hello World 应用。</p>'],
                    ['title' => '1.4 模板语法', 'level' => 2, 'order' => 4, 'content' => '<p>Vue.js 使用基于 HTML 的模板语法。</p>'],
                    ['title' => '1.5 指令系统', 'level' => 2, 'order' => 5, 'content' => '<p>v-if、v-for、v-bind 等常用指令的使用。</p>'],
                ]
            ],
            [
                'title' => '第二章 组件系统',
                'level' => 1,
                'order' => 2,
                'content' => '<p>组件是 Vue.js 的核心概念之一，本章深入讲解组件的使用。</p><h3>主要内容：</h3><ul><li>组件基础</li><li>组件通信</li><li>插槽</li><li>动态组件</li></ul>',
                'children' => [
                    ['title' => '2.1 组件基础', 'level' => 2, 'order' => 1, 'content' => '<p>如何创建和使用组件。</p>'],
                    ['title' => '2.2 组件通信', 'level' => 2, 'order' => 2, 'content' => '<p>父子组件之间的数据传递。</p>'],
                    ['title' => '2.3 Props 和 Events', 'level' => 2, 'order' => 3, 'content' => '<p>使用 Props 传递数据，使用 Events 传递事件。</p>'],
                    ['title' => '2.4 插槽（Slots）', 'level' => 2, 'order' => 4, 'content' => '<p>插槽的使用方法和应用场景。</p>'],
                ]
            ],
            [
                'title' => '第三章 响应式数据',
                'level' => 1,
                'order' => 3,
                'content' => '<p>Vue.js 的响应式系统是其核心特性之一。</p><h3>主要内容：</h3><ul><li>响应式原理</li><li>计算属性</li><li>侦听器</li><li>响应式 API</li></ul>',
                'children' => [
                    ['title' => '3.1 响应式原理', 'level' => 2, 'order' => 1, 'content' => '<p>Vue.js 如何实现数据的响应式。</p>'],
                    ['title' => '3.2 计算属性', 'level' => 2, 'order' => 2, 'content' => '<p>computed 属性的使用和优势。</p>'],
                    ['title' => '3.3 侦听器', 'level' => 2, 'order' => 3, 'content' => '<p>watch 的使用方法和应用场景。</p>'],
                    ['title' => '3.4 响应式 API', 'level' => 2, 'order' => 4, 'content' => '<p>ref、reactive、toRefs 等 API 的使用。</p>'],
                ]
            ],
            [
                'title' => '第四章 生命周期',
                'level' => 1,
                'order' => 4,
                'content' => '<p>了解 Vue 组件的生命周期钩子。</p><h3>主要内容：</h3><ul><li>生命周期概述</li><li>创建阶段</li><li>更新阶段</li><li>销毁阶段</li></ul>',
                'children' => [
                    ['title' => '4.1 生命周期概述', 'level' => 2, 'order' => 1, 'content' => '<p>Vue 组件的生命周期流程。</p>'],
                    ['title' => '4.2 创建阶段钩子', 'level' => 2, 'order' => 2, 'content' => '<p>beforeCreate、created、beforeMount、mounted 等钩子。</p>'],
                    ['title' => '4.3 更新阶段钩子', 'level' => 2, 'order' => 3, 'content' => '<p>beforeUpdate、updated 钩子的使用。</p>'],
                    ['title' => '4.4 销毁阶段钩子', 'level' => 2, 'order' => 4, 'content' => '<p>beforeUnmount、unmounted 钩子的使用。</p>'],
                ]
            ],
            [
                'title' => '第五章 路由管理',
                'level' => 1,
                'order' => 5,
                'content' => '<p>使用 Vue Router 进行单页应用的路由管理。</p><h3>主要内容：</h3><ul><li>Vue Router 基础</li><li>路由配置</li><li>导航守卫</li><li>动态路由</li></ul>',
                'children' => [
                    ['title' => '5.1 Vue Router 基础', 'level' => 2, 'order' => 1, 'content' => '<p>Vue Router 的安装和基本使用。</p>'],
                    ['title' => '5.2 路由配置', 'level' => 2, 'order' => 2, 'content' => '<p>如何配置路由表和路由规则。</p>'],
                    ['title' => '5.3 导航守卫', 'level' => 2, 'order' => 3, 'content' => '<p>全局守卫、路由守卫、组件守卫的使用。</p>'],
                    ['title' => '5.4 动态路由', 'level' => 2, 'order' => 4, 'content' => '<p>动态路由参数和查询参数的使用。</p>'],
                ]
            ],
            [
                'title' => '第六章 状态管理',
                'level' => 1,
                'order' => 6,
                'content' => '<p>使用 Pinia 或 Vuex 进行状态管理。</p><h3>主要内容：</h3><ul><li>状态管理概述</li><li>Pinia 基础</li><li>Store 的使用</li><li>状态持久化</li></ul>',
                'children' => [
                    ['title' => '6.1 状态管理概述', 'level' => 2, 'order' => 1, 'content' => '<p>为什么需要状态管理，何时使用状态管理。</p>'],
                    ['title' => '6.2 Pinia 基础', 'level' => 2, 'order' => 2, 'content' => '<p>Pinia 的安装和基本概念。</p>'],
                    ['title' => '6.3 Store 的使用', 'level' => 2, 'order' => 3, 'content' => '<p>如何定义和使用 Store。</p>'],
                    ['title' => '6.4 状态持久化', 'level' => 2, 'order' => 4, 'content' => '<p>如何持久化状态数据。</p>'],
                ]
            ],
            [
                'title' => '第七章 高级特性',
                'level' => 1,
                'order' => 7,
                'content' => '<p>Vue.js 的高级特性和最佳实践。</p><h3>主要内容：</h3><ul><li>自定义指令</li><li>插件系统</li><li>渲染函数</li><li>性能优化</li></ul>',
                'children' => [
                    ['title' => '7.1 自定义指令', 'level' => 2, 'order' => 1, 'content' => '<p>如何创建和使用自定义指令。</p>'],
                    ['title' => '7.2 插件系统', 'level' => 2, 'order' => 2, 'content' => '<p>Vue 插件的开发和安装。</p>'],
                    ['title' => '7.3 渲染函数', 'level' => 2, 'order' => 3, 'content' => '<p>使用渲染函数创建组件。</p>'],
                    ['title' => '7.4 性能优化', 'level' => 2, 'order' => 4, 'content' => '<p>Vue 应用的性能优化技巧。</p>'],
                ]
            ],
            [
                'title' => '第八章 项目实战',
                'level' => 1,
                'order' => 8,
                'content' => '<p>通过实际项目案例巩固所学知识。</p><h3>主要内容：</h3><ul><li>项目搭建</li><li>功能开发</li><li>测试和调试</li><li>部署上线</li></ul>',
                'children' => [
                    ['title' => '8.1 项目搭建', 'level' => 2, 'order' => 1, 'content' => '<p>使用 Vue CLI 或 Vite 搭建项目。</p>'],
                    ['title' => '8.2 功能开发', 'level' => 2, 'order' => 2, 'content' => '<p>逐步开发项目功能模块。</p>'],
                    ['title' => '8.3 测试和调试', 'level' => 2, 'order' => 3, 'content' => '<p>编写测试用例和调试技巧。</p>'],
                    ['title' => '8.4 部署上线', 'level' => 2, 'order' => 4, 'content' => '<p>项目的构建和部署流程。</p>'],
                ]
            ],
        ];

        $this->info("开始为《{$book->title}》生成章节...");

        // 检查是否已有章节
        $existingChapters = BookChapter::where('book_id', $bookId)->count();
        if ($existingChapters > 0) {
            if (!$this->confirm("该书已有 {$existingChapters} 个章节，是否继续添加？", true)) {
                $this->info('操作已取消');
                return 0;
            }
        }

        $createdCount = 0;
        $bar = $this->output->createProgressBar(count($chaptersData));
        $bar->start();

        foreach ($chaptersData as $chapterData) {
            // 创建一级章节
            $parentChapter = BookChapter::create([
                'book_id' => $bookId,
                'title' => $chapterData['title'],
                'order' => $chapterData['order'],
                'parent_id' => null,
                'level' => $chapterData['level'],
                'content' => $chapterData['content'] ?? null,
            ]);
            $createdCount++;

            // 创建子章节
            if (isset($chapterData['children']) && is_array($chapterData['children'])) {
                foreach ($chapterData['children'] as $childData) {
                    BookChapter::create([
                        'book_id' => $bookId,
                        'title' => $childData['title'],
                        'order' => $childData['order'],
                        'parent_id' => $parentChapter->id,
                        'level' => $childData['level'],
                        'content' => $childData['content'] ?? null,
                    ]);
                    $createdCount++;
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("完成！共创建了 {$createdCount} 个章节");
        
        return 0;
    }
}
