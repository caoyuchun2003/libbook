<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\BookChapter;
use Illuminate\Console\Command;

class SeedDemoBookContent extends Command
{
    protected $signature = 'books:seed-demo-content {--force : 覆盖已有简介与章节}';
    protected $description = '为图书灌入演示用内容简介、作者简介与章节目录正文';

    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $books = Book::with('category')->orderBy('id')->get();

        if ($books->isEmpty()) {
            $this->error('没有图书可填充');
            return self::FAILURE;
        }

        $introUpdated = 0;
        $chapterBooks = 0;

        foreach ($books as $book) {
            $intro = $this->introFor($book);
            if ($intro && ($force || empty($book->content_intro))) {
                $book->content_intro = $intro['content_intro'];
                $book->author_intro = $intro['author_intro'];
                $book->save();
                $introUpdated++;
                $this->line("简介: {$book->title}");
            }

            $existing = BookChapter::where('book_id', $book->id)->count();
            if ($existing > 0 && !$force) {
                $this->line("跳过章节（已有 {$existing}）: {$book->title}");
                continue;
            }

            if ($existing > 0 && $force) {
                BookChapter::where('book_id', $book->id)->delete();
            }

            $toc = $this->chaptersFor($book);
            $created = $this->insertChapters($book->id, $toc);
            $chapterBooks++;
            $this->info("章节: {$book->title} (+{$created})");
        }

        $this->newLine();
        $this->info("完成：更新简介 {$introUpdated} 本，写入章节 {$chapterBooks} 本。");
        return self::SUCCESS;
    }

    private function insertChapters(int $bookId, array $chaptersData): int
    {
        $created = 0;
        foreach ($chaptersData as $chapterData) {
            $parent = BookChapter::create([
                'book_id' => $bookId,
                'title' => $chapterData['title'],
                'order' => $chapterData['order'],
                'parent_id' => null,
                'level' => $chapterData['level'] ?? 1,
                'content' => $chapterData['content'] ?? null,
            ]);
            $created++;

            foreach ($chapterData['children'] ?? [] as $child) {
                BookChapter::create([
                    'book_id' => $bookId,
                    'title' => $child['title'],
                    'order' => $child['order'],
                    'parent_id' => $parent->id,
                    'level' => $child['level'] ?? 2,
                    'content' => $child['content'] ?? null,
                ]);
                $created++;
            }
        }

        return $created;
    }

    private function p(string $title, string $body): string
    {
        return "<p><strong>{$title}</strong></p><p>{$body}</p>"
            . '<p>本节为演示内容，便于体验目录展开、阅读器切换与正文排版。你可以在后台继续编辑真实章节。</p>';
    }

    private function chapter(string $title, int $order, string $summary, array $children = []): array
    {
        return [
            'title' => $title,
            'level' => 1,
            'order' => $order,
            'content' => $this->p($title, $summary),
            'children' => $children,
        ];
    }

    private function child(string $title, int $order, string $summary): array
    {
        return [
            'title' => $title,
            'level' => 2,
            'order' => $order,
            'content' => $this->p($title, $summary),
        ];
    }

    private function introFor(Book $book): ?array
    {
        $map = [
            'Vue.js 实战' => [
                'content_intro' => '<p>《Vue.js 实战》从组件化、响应式到工程化实践，帮助你快速上手现代前端开发。</p><ul><li>模板与指令</li><li>组件通信</li><li>路由与状态</li><li>项目实战</li></ul>',
                'author_intro' => '<p><strong>张三</strong>，前端工程师，长期从事 Vue 生态相关开发与教学。</p>',
            ],
            'Laravel 框架开发' => [
                'content_intro' => '<p>《Laravel 框架开发》覆盖路由、Eloquent、认证、队列等后端核心能力，适合 Web 应用实战。</p>',
                'author_intro' => '<p><strong>李四</strong>，PHP / Laravel 开发者，关注可维护的后端架构。</p>',
            ],
            'JavaScript 高级程序设计' => [
                'content_intro' => '<p>深入讲解 JavaScript 语言核心：类型、作用域、异步、原型与工程实践。</p>',
                'author_intro' => '<p><strong>Matt Frisbie</strong>，资深 Web 开发者与技术作者。</p>',
            ],
            'Python 编程：从入门到实践' => [
                'content_intro' => '<p>以项目驱动学习 Python：语法基础、数据结构、文件处理与小项目实战。</p>',
                'author_intro' => '<p><strong>Eric Matthes</strong>，编程教育者，擅长面向初学者的讲解方式。</p>',
            ],
            '深入理解计算机系统' => [
                'content_intro' => '<p>从程序员视角理解计算机系统：数据表示、程序机器级表示、存储器与链接。</p>',
                'author_intro' => '<p><strong>Randal E. Bryant</strong>，计算机系统领域教育与研究者。</p>',
            ],
            '算法导论' => [
                'content_intro' => '<p>系统介绍算法设计与分析：排序、树与图、动态规划与贪心等经典主题。</p>',
                'author_intro' => '<p><strong>Thomas H. Cormen</strong>，《算法导论》作者之一。</p>',
            ],
            '红楼梦' => [
                'content_intro' => '<p>以贾府兴衰与宝黛爱情为主线，展现人情世态与时代变迁的古典文学巨著。</p>',
                'author_intro' => '<p><strong>曹雪芹</strong>，清代小说家，《红楼梦》作者。</p>',
            ],
            '西游记' => [
                'content_intro' => '<p>唐僧师徒西天取经，历经磨难，既是神魔传奇，也是成长与坚持的寓言。</p>',
                'author_intro' => '<p><strong>吴承恩</strong>，明代小说家，《西游记》作者。</p>',
            ],
            '水浒传' => [
                'content_intro' => '<p>梁山好汉聚义故事，刻画人物性格鲜明，反映社会矛盾与江湖义气。</p>',
                'author_intro' => '<p><strong>施耐庵</strong>，元末明初小说家，《水浒传》作者。</p>',
            ],
            '三国演义' => [
                'content_intro' => '<p>东汉末年到三国鼎立的历史演义，智谋、忠义与乱世英雄同台登场。</p>',
                'author_intro' => '<p><strong>罗贯中</strong>，元末明初小说家，《三国演义》作者。</p>',
            ],
            '围城' => [
                'content_intro' => '<p>以方鸿渐的婚姻与事业为主线，讽刺知识分子困境与社会荒诞，提出著名的“围城”隐喻。</p>',
                'author_intro' => '<p><strong>钱钟书</strong>，学者、作家，《围城》作者。</p>',
            ],
            '活着' => [
                'content_intro' => '<p>讲述福贵一生的苦难与坚韧，探讨“活着”本身的意义。</p>',
                'author_intro' => '<p><strong>余华</strong>，当代作家，《活着》作者。</p>',
            ],
        ];

        return $map[$book->title] ?? [
            'content_intro' => "<p>《{$book->title}》是宇春书城的演示图书。以下内容用于体验详情页与阅读器。</p>",
            'author_intro' => '<p><strong>' . e($book->author) . '</strong>，本书作者（演示数据）。</p>',
        ];
    }

    private function chaptersFor(Book $book): array
    {
        $title = $book->title;
        $templates = [
            'Vue.js 实战' => [
                $this->chapter('第一章 入门与环境', 1, '了解 Vue 是什么，以及如何快速跑起来。', [
                    $this->child('1.1 为什么选择 Vue', 1, '渐进式框架的定位与适用场景。'),
                    $this->child('1.2 创建第一个应用', 2, '用最少代码完成 Hello World。'),
                    $this->child('1.3 开发工具建议', 3, 'Vite、DevTools 与常用扩展。'),
                ]),
                $this->chapter('第二章 模板与指令', 2, '掌握模板语法与常用指令。', [
                    $this->child('2.1 插值与绑定', 1, '文本插值、属性绑定与样式类。'),
                    $this->child('2.2 条件与列表', 2, 'v-if / v-show / v-for 的使用差异。'),
                ]),
                $this->chapter('第三章 组件化', 3, '拆分界面，形成可复用组件。', [
                    $this->child('3.1 单文件组件', 1, 'SFC 结构与局部注册。'),
                    $this->child('3.2 Props 与事件', 2, '父子通信的基本约定。'),
                ]),
                $this->chapter('第四章 路由与状态', 4, '单页应用导航与跨组件状态。', [
                    $this->child('4.1 Vue Router 基础', 1, '路由表、参数与导航守卫。'),
                    $this->child('4.2 Pinia 入门', 2, '定义 store 与在组件中使用。'),
                ]),
            ],
            'Laravel 框架开发' => [
                $this->chapter('第一章 Laravel 概览', 1, '了解框架结构与请求生命周期。', [
                    $this->child('1.1 安装与目录', 1, 'composer create-project 与关键目录。'),
                    $this->child('1.2 路由与控制器', 2, 'web/api 路由与控制器职责。'),
                ]),
                $this->chapter('第二章 Eloquent 与数据库', 2, '用 ORM 读写数据。', [
                    $this->child('2.1 Migration', 1, '版本化管理表结构。'),
                    $this->child('2.2 模型关联', 2, '一对多、多对多常见写法。'),
                ]),
                $this->chapter('第三章 认证与授权', 3, '登录态、Token 与权限。', [
                    $this->child('3.1 Sanctum', 1, 'API Token 认证流程。'),
                    $this->child('3.2 Policy', 2, '资源级权限控制。'),
                ]),
                $this->chapter('第四章 实战接口', 4, '设计 REST API 与资源转换。', [
                    $this->child('4.1 Resource', 1, '统一 API 输出结构。'),
                    $this->child('4.2 验证与异常', 2, 'Form Request 与错误响应。'),
                ]),
            ],
            'JavaScript 高级程序设计' => [
                $this->chapter('第一章 语言基础', 1, '类型、变量与运算符。', [
                    $this->child('1.1 数据类型', 1, '原始类型与引用类型。'),
                    $this->child('1.2 作用域与闭包', 2, '词法作用域与常见陷阱。'),
                ]),
                $this->chapter('第二章 对象与原型', 2, '理解原型链与继承。', [
                    $this->child('2.1 原型链', 1, '[[Prototype]] 查找机制。'),
                    $this->child('2.2 class 语法', 2, '类与继承的语法糖。'),
                ]),
                $this->chapter('第三章 异步编程', 3, '从回调到 Promise / async。', [
                    $this->child('3.1 Event Loop', 1, '宏任务与微任务。'),
                    $this->child('3.2 async/await', 2, '可读的异步流程控制。'),
                ]),
                $this->chapter('第四章 工程实践', 4, '模块化、打包与调试。', [
                    $this->child('4.1 ES Module', 1, 'import / export 约定。'),
                    $this->child('4.2 调试技巧', 2, '断点、性能与常见工具。'),
                ]),
            ],
            'Python 编程：从入门到实践' => [
                $this->chapter('第一章 起步', 1, '安装环境并写出第一段代码。', [
                    $this->child('1.1 安装 Python', 1, '解释器与虚拟环境。'),
                    $this->child('1.2 变量与输入输出', 2, '基础交互程序。'),
                ]),
                $this->chapter('第二章 数据结构', 2, '列表、字典与常用操作。', [
                    $this->child('2.1 列表与切片', 1, '增删改查与推导式。'),
                    $this->child('2.2 字典', 2, '键值映射与遍历。'),
                ]),
                $this->chapter('第三章 函数与类', 3, '组织代码的基本方式。', [
                    $this->child('3.1 函数', 1, '参数、返回值与作用域。'),
                    $this->child('3.2 类与对象', 2, '面向对象入门。'),
                ]),
                $this->chapter('第四章 小项目', 4, '把语法串成可运行程序。', [
                    $this->child('4.1 文件读写', 1, '处理文本与 CSV。'),
                    $this->child('4.2 简单爬虫/可视化', 2, '选择一个小方向练手。'),
                ]),
            ],
            '深入理解计算机系统' => [
                $this->chapter('第一章 计算机系统漫游', 1, '建立系统全景。', [
                    $this->child('1.1 信息就是位+上下文', 1, '程序如何被表示。'),
                    $this->child('1.2 编译系统', 2, '从源码到可执行文件。'),
                ]),
                $this->chapter('第二章 数据的机器级表示', 2, '整数与浮点。', [
                    $this->child('2.1 整数编码', 1, '补码与溢出。'),
                    $this->child('2.2 浮点数', 2, 'IEEE 表示与精度。'),
                ]),
                $this->chapter('第三章 程序的机器级表示', 3, '汇编视角看程序。', [
                    $this->child('3.1 汇编基础', 1, '寄存器与寻址。'),
                    $this->child('3.2 控制与过程', 2, '条件跳转与调用约定。'),
                ]),
                $this->chapter('第四章 存储器层次结构', 4, '缓存如何影响性能。', [
                    $this->child('4.1 局部性', 1, '时间局部性与空间局部性。'),
                    $this->child('4.2 缓存命中', 2, '写回与替换策略概览。'),
                ]),
            ],
            '算法导论' => [
                $this->chapter('第一章 算法引论', 1, '复杂度与渐进记号。', [
                    $this->child('1.1 问题与算法', 1, '正确性与效率。'),
                    $this->child('1.2 渐近分析', 2, '大 O 记号的含义。'),
                ]),
                $this->chapter('第二章 排序', 2, '比较排序与线性时间排序。', [
                    $this->child('2.1 归并排序', 1, '分治与合并。'),
                    $this->child('2.2 快速排序', 2, '划分与期望复杂度。'),
                ]),
                $this->chapter('第三章 基本数据结构', 3, '栈、队列、树与哈希。', [
                    $this->child('3.1 哈希表', 1, '冲突与装载因子。'),
                    $this->child('3.2 二叉搜索树', 2, '查找、插入与删除。'),
                ]),
                $this->chapter('第四章 图算法', 4, '遍历、最短路与生成树。', [
                    $this->child('4.1 BFS / DFS', 1, '图遍历基础。'),
                    $this->child('4.2 最短路径', 2, 'Dijkstra 思路概述。'),
                ]),
            ],
            '红楼梦' => [
                $this->chapter('第一回 甄士隐梦幻识通灵', 1, '开篇点题，引出通灵宝玉。', [
                    $this->child('1.1 顽石入世', 1, '神话引子与全书缘起。'),
                    $this->child('1.2 甄士隐出家', 2, '荣枯无常的伏笔。'),
                ]),
                $this->chapter('第三回 贾雨村夤缘复旧职', 2, '黛玉进贾府。', [
                    $this->child('3.1 初见贾府', 1, '礼仪与人物登场。'),
                    $this->child('3.2 宝黛初会', 2, '似曾相识的一见。'),
                ]),
                $this->chapter('第五回 游幻境指迷十二钗', 3, '太虚幻境与判词。', [
                    $this->child('5.1 警幻仙姑', 1, '以幻写真。'),
                    $this->child('5.2 金陵十二钗', 2, '人物命运的预示。'),
                ]),
                $this->chapter('第二十三回 西厢记妙词通戏语', 4, '宝黛共读西厢。', [
                    $this->child('23.1 大观园居住', 1, '少年们的日常空间。'),
                    $this->child('23.2 情意暗生', 2, '诗词与心事。'),
                ]),
            ],
            '西游记' => [
                $this->chapter('第一回 灵根育孕源流出', 1, '石猴出世。', [
                    $this->child('1.1 花果山', 1, '洞天福地。'),
                    $this->child('1.2 寻师访道', 2, '求长生之始。'),
                ]),
                $this->chapter('第七回 八卦炉中逃大圣', 2, '大闹天宫余波。', [
                    $this->child('7.1 炼丹炉', 1, '金睛火眼的由来。'),
                    $this->child('7.2 五行山', 2, '被压五百年。'),
                ]),
                $this->chapter('第十四回 心猿归正', 3, '悟空保唐僧。', [
                    $this->child('14.1 揭帖出山', 1, '取经缘起。'),
                    $this->child('14.2 六耳未现前', 2, '师徒关系初建。'),
                ]),
                $this->chapter('第五十九回 唐三藏路阻火焰山', 4, '三调芭蕉扇。', [
                    $this->child('59.1 火焰山', 1, '行程受阻。'),
                    $this->child('59.2 铁扇公主', 2, '借扇风波。'),
                ]),
            ],
            '水浒传' => [
                $this->chapter('第三回 史大郎夜走华阴县', 1, '鲁提辖拳打镇关西。', [
                    $this->child('3.1 渭州遇金翠莲', 1, '路见不平。'),
                    $this->child('3.2 拳打郑屠', 2, '鲁达性格初显。'),
                ]),
                $this->chapter('第十回 林教头风雪山神庙', 2, '逼上梁山的典型。', [
                    $this->child('10.1 沧州牢城', 1, '被发配的日子。'),
                    $this->child('10.2 山神庙', 2, '雪夜复仇。'),
                ]),
                $this->chapter('第二十三回 横海郡柴进留宾', 3, '武松打虎。', [
                    $this->child('23.1 景阳冈', 1, '酒后遇虎。'),
                    $this->child('23.2 阳谷县', 2, '打虎成名。'),
                ]),
                $this->chapter('第七十一回 忠义堂石碣受天文', 4, '一百单八将排座次。', [
                    $this->child('71.1 聚义', 1, '梁山盛时。'),
                    $this->child('71.2 座次', 2, '星宿名号。'),
                ]),
            ],
            '三国演义' => [
                $this->chapter('第一回 宴桃园豪杰三结义', 1, '刘关张起事。', [
                    $this->child('1.1 黄巾起义', 1, '乱世开端。'),
                    $this->child('1.2 桃园结义', 2, '忠义定盟。'),
                ]),
                $this->chapter('第三十八回 定三分隆中决策', 2, '三顾茅庐。', [
                    $this->child('38.1 卧龙出山', 1, '刘备得孔明。'),
                    $this->child('38.2 隆中对', 2, '三分天下的蓝图。'),
                ]),
                $this->chapter('第四十九回 七星坛诸葛祭风', 3, '赤壁之战关键一幕。', [
                    $this->child('49.1 借东风', 1, '火攻条件。'),
                    $this->child('49.2 周瑜用计', 2, '孙刘联合。'),
                ]),
                $this->chapter('第一百四回 陨大星汉丞相归天', 4, '诸葛亮病逝五丈原。', [
                    $this->child('104.1 北伐', 1, '鞠躬尽瘁。'),
                    $this->child('104.2 遗计', 2, '身后安排。'),
                ]),
            ],
            '围城' => [
                $this->chapter('第一章 归国邮轮', 1, '方鸿渐回国途中。', [
                    $this->child('1.1 假博士', 1, '文凭与虚荣。'),
                    $this->child('1.2 船上众人', 2, '群像登场。'),
                ]),
                $this->chapter('第三章 上海交际', 2, '苏文纨与唐晓芙。', [
                    $this->child('3.1 情意错位', 1, '追求与误会。'),
                    $this->child('3.2 朋友圈', 2, '知识分子社交。'),
                ]),
                $this->chapter('第五章 三闾大学', 3, '内地任教的荒诞。', [
                    $this->child('5.1 人事倾轧', 1, '学院政治。'),
                    $this->child('5.2 婚姻围城', 2, '走进另一座城。'),
                ]),
                $this->chapter('第九章 围城内外', 4, '婚姻与出路的困局。', [
                    $this->child('9.1 吵闹与冷战', 1, '日常消耗。'),
                    $this->child('9.2 出走与回望', 2, '城外的人想进来。'),
                ]),
            ],
            '活着' => [
                $this->chapter('第一章 少年赌徒', 1, '福贵败光家产。', [
                    $this->child('1.1 赌场', 1, '豪赌的开始。'),
                    $this->child('1.2 家道中落', 2, '从少爷到穷人。'),
                ]),
                $this->chapter('第三章 战乱与饥荒', 2, '时代洪流中的个人。', [
                    $this->child('3.1 被抓壮丁', 1, '离乡与幸存。'),
                    $this->child('3.2 归来', 2, '亲人尚在。'),
                ]),
                $this->chapter('第五章 亲人相继离去', 3, '苦难的叠加。', [
                    $this->child('5.1 有庆', 1, '孩子的命运。'),
                    $this->child('5.2 家珍', 2, '相依为命的日子。'),
                ]),
                $this->chapter('第七章 与老牛为伴', 4, '活着本身。', [
                    $this->child('7.1 黄昏', 1, '讲述一生。'),
                    $this->child('7.2 余声', 2, '人与牛的沉默陪伴。'),
                ]),
            ],
        ];

        if (isset($templates[$title])) {
            return $templates[$title];
        }

        // generic fallback
        return [
            $this->chapter('第一章 开篇', 1, "《{$title}》演示开篇。", [
                $this->child('1.1 背景', 1, '写作背景与阅读提示。'),
                $this->child('1.2 结构', 2, '本书结构概览。'),
            ]),
            $this->chapter('第二章 展开', 2, '核心内容展开。', [
                $this->child('2.1 要点一', 1, '第一个关键主题。'),
                $this->child('2.2 要点二', 2, '第二个关键主题。'),
            ]),
            $this->chapter('第三章 实践', 3, '结合例子巩固。', [
                $this->child('3.1 例子', 1, '演示案例。'),
                $this->child('3.2 小结', 2, '本章回顾。'),
            ]),
            $this->chapter('第四章 收束', 4, '总结与延伸阅读。', [
                $this->child('4.1 总结', 1, '要点回顾。'),
                $this->child('4.2 延伸', 2, '下一步可以读什么。'),
            ]),
        ];
    }
}
