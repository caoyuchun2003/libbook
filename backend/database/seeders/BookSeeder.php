<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Category;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        // 获取分类 ID
        $computerCategory = Category::where('name', '计算机')->first();
        $literatureCategory = Category::where('name', '文学')->first();
        $historyCategory = Category::where('name', '历史')->first();
        $scienceCategory = Category::where('name', '科学')->first();

        // 创建图书
        $books = [
            // 计算机类
            [
                'title' => 'Vue.js 实战',
                'author' => '张三',
                'isbn' => '978-7-111-12345-6',
                'description' => 'Vue.js 开发指南，从入门到精通',
                'category_id' => $computerCategory->id,
                'total_copies' => 10,
                'available_copies' => 10,
                'price' => 89.00,
                'published_at' => '2023-01-01',
            ],
            [
                'title' => 'Laravel 框架开发',
                'author' => '李四',
                'isbn' => '978-7-111-12345-7',
                'description' => 'Laravel PHP 框架开发教程',
                'category_id' => $computerCategory->id,
                'total_copies' => 8,
                'available_copies' => 8,
                'price' => 79.00,
                'published_at' => '2023-02-01',
            ],
            [
                'title' => 'JavaScript 高级程序设计',
                'author' => 'Matt Frisbie',
                'isbn' => '978-7-115-12345-1',
                'description' => 'JavaScript 编程经典教材，深入理解 JavaScript 核心概念',
                'content_intro' => '<p>《JavaScript 高级程序设计》是一本全面介绍 JavaScript 语言核心特性的经典教材。本书深入探讨了 JavaScript 的<strong>核心概念</strong>、<strong>面向对象编程</strong>、<strong>函数式编程</strong>等高级主题。</p><p>本书涵盖了以下主要内容：</p><ul><li>JavaScript 基础语法和数据类型</li><li>作用域、闭包和 this 绑定</li><li>原型链和继承机制</li><li>异步编程和 Promise</li><li>模块化开发</li><li>错误处理和调试技巧</li></ul><p>无论你是初学者还是有经验的开发者，这本书都能帮助你深入理解 JavaScript 的精髓，提升编程技能。</p>',
                'author_intro' => '<p><strong>Matt Frisbie</strong> 是一位资深的 Web 开发专家，拥有多年的 JavaScript 开发经验。他曾参与多个大型 Web 项目的开发，对 JavaScript 语言有着深入的理解。</p><p>Matt 致力于将复杂的技术概念以通俗易懂的方式传递给读者，他的作品深受全球开发者的喜爱。除了技术写作，他还活跃于开源社区，为多个 JavaScript 项目贡献代码。</p>',
                'category_id' => $computerCategory->id,
                'total_copies' => 12,
                'available_copies' => 12,
                'price' => 129.00,
                'published_at' => '2020-05-01',
            ],
            [
                'title' => 'Python 编程：从入门到实践',
                'author' => 'Eric Matthes',
                'isbn' => '978-7-115-12345-2',
                'description' => 'Python 编程入门经典，适合初学者',
                'content_intro' => '<p>《Python 编程：从入门到实践》是一本适合初学者的 Python 编程入门书籍。本书采用<em>实践驱动</em>的教学方式，通过丰富的实例帮助读者快速掌握 Python 编程。</p><h3>主要内容包括：</h3><ol><li><strong>Python 基础</strong>：变量、数据类型、控制结构</li><li><strong>数据结构</strong>：列表、字典、元组等</li><li><strong>函数和类</strong>：代码组织和复用</li><li><strong>文件处理</strong>：读写文件和数据持久化</li><li><strong>项目实战</strong>：数据可视化、Web 应用开发</li></ol><p>本书不仅讲解语法，更重要的是培养编程思维，让读者能够独立解决实际问题。</p>',
                'author_intro' => '<p><strong>Eric Matthes</strong> 是一位高中科学和数学教师，同时也是一位 Python 编程爱好者。他致力于帮助初学者轻松入门编程，让编程学习变得有趣而高效。</p><p>Eric 拥有丰富的教学经验，善于将复杂的概念分解为易于理解的小步骤。他的教学风格深受学生喜爱，这本书也成为了 Python 入门领域的经典之作。</p>',
                'category_id' => $computerCategory->id,
                'total_copies' => 15,
                'available_copies' => 15,
                'price' => 89.00,
                'published_at' => '2016-07-01',
            ],
            [
                'title' => '深入理解计算机系统',
                'author' => 'Randal E. Bryant',
                'isbn' => '978-7-111-12345-3',
                'description' => '计算机系统经典教材，深入理解计算机底层原理',
                'category_id' => $computerCategory->id,
                'total_copies' => 6,
                'available_copies' => 6,
                'price' => 139.00,
                'published_at' => '2016-11-01',
            ],
            [
                'title' => '算法导论',
                'author' => 'Thomas H. Cormen',
                'isbn' => '978-7-111-12345-4',
                'description' => '算法与数据结构经典教材',
                'category_id' => $computerCategory->id,
                'total_copies' => 5,
                'available_copies' => 5,
                'price' => 128.00,
                'published_at' => '2012-12-01',
            ],
            [
                'title' => 'React 技术栈',
                'author' => 'Alex Banks',
                'isbn' => '978-7-115-12345-3',
                'description' => 'React 框架开发完整指南',
                'category_id' => $computerCategory->id,
                'total_copies' => 9,
                'available_copies' => 9,
                'price' => 99.00,
                'published_at' => '2020-03-01',
            ],
            [
                'title' => 'MySQL 必知必会',
                'author' => 'Ben Forta',
                'isbn' => '978-7-115-12345-4',
                'description' => 'MySQL 数据库入门教程',
                'category_id' => $computerCategory->id,
                'total_copies' => 11,
                'available_copies' => 11,
                'price' => 49.00,
                'published_at' => '2009-01-01',
            ],
            // 文学类
            [
                'title' => '红楼梦',
                'author' => '曹雪芹',
                'isbn' => '978-7-111-12345-8',
                'description' => '中国古典四大名著之一',
                'content_intro' => '<p>《红楼梦》是中国古典小说的巅峰之作，被誉为"中国封建社会的百科全书"。小说以<strong>贾、史、王、薛</strong>四大家族的兴衰为背景，以贾宝玉、林黛玉、薛宝钗的爱情悲剧为主线，深刻描绘了封建社会的种种矛盾和危机。</p><p>作品通过细腻的笔触，塑造了众多栩栩如生的人物形象：</p><ul><li>多情而叛逆的<em>贾宝玉</em></li><li>才情横溢却命运多舛的<em>林黛玉</em></li><li>端庄贤淑的<em>薛宝钗</em></li><li>精明能干的<em>王熙凤</em></li></ul><p>小说不仅展现了封建贵族的生活画卷，更深刻揭示了人性的复杂和社会的变迁，具有极高的文学价值和思想深度。</p>',
                'author_intro' => '<p><strong>曹雪芹</strong>（约1715年—约1763年），名霑，字梦阮，号雪芹，清代著名小说家。他出身于一个"百年望族"的大官僚地主家庭，后因家族衰落，生活贫困。</p><p>曹雪芹经历了从富贵到贫困的巨大人生转折，这种经历使他对封建社会有了深刻的认识。他花费十余年时间创作《红楼梦》，"批阅十载，增删五次"，最终完成了这部不朽的文学巨著。</p><p>曹雪芹的创作才华和深刻思想，使《红楼梦》成为中国文学史上的一座丰碑，对后世文学产生了深远的影响。</p>',
                'category_id' => $literatureCategory->id,
                'total_copies' => 5,
                'available_copies' => 5,
                'price' => 68.00,
                'published_at' => '1791-01-01',
            ],
            [
                'title' => '西游记',
                'author' => '吴承恩',
                'isbn' => '978-7-111-12345-9',
                'description' => '中国古典四大名著之一',
                'category_id' => $literatureCategory->id,
                'total_copies' => 5,
                'available_copies' => 5,
                'price' => 65.00,
                'published_at' => '1592-01-01',
            ],
            [
                'title' => '水浒传',
                'author' => '施耐庵',
                'isbn' => '978-7-111-12346-0',
                'description' => '中国古典四大名著之一',
                'category_id' => $literatureCategory->id,
                'total_copies' => 5,
                'available_copies' => 5,
                'price' => 66.00,
                'published_at' => '1589-01-01',
            ],
            [
                'title' => '三国演义',
                'author' => '罗贯中',
                'isbn' => '978-7-111-12346-1',
                'description' => '中国古典四大名著之一',
                'category_id' => $literatureCategory->id,
                'total_copies' => 5,
                'available_copies' => 5,
                'price' => 67.00,
                'published_at' => '1522-01-01',
            ],
            [
                'title' => '围城',
                'author' => '钱钟书',
                'isbn' => '978-7-111-12346-2',
                'description' => '现代文学经典作品',
                'category_id' => $literatureCategory->id,
                'total_copies' => 8,
                'available_copies' => 8,
                'price' => 39.00,
                'published_at' => '1947-01-01',
            ],
            [
                'title' => '活着',
                'author' => '余华',
                'isbn' => '978-7-111-12346-3',
                'description' => '当代文学经典，讲述人生的苦难与坚韧',
                'content_intro' => '<p>《活着》是余华的代表作之一，讲述了一个普通农民<strong>福贵</strong>的一生。小说以第一人称的叙述方式，展现了福贵从地主少爷到贫苦农民的跌宕人生。</p><p>福贵的一生充满了苦难：</p><ul><li>失去了所有的家产</li><li>经历了战争和饥荒</li><li>失去了所有的亲人</li><li>最终只剩下他和一头老牛相依为命</li></ul><p>然而，即使在最绝望的时刻，福贵依然选择<em>活着</em>。小说通过福贵的故事，深刻探讨了生命的意义、苦难的价值以及人性的坚韧。</p><blockquote>"人是为了活着本身而活着的，而不是为了活着之外的任何事物而活着。"</blockquote>',
                'author_intro' => '<p><strong>余华</strong>（1960年—），中国当代著名作家，浙江海盐人。他是中国先锋派文学的代表人物之一，作品以深刻的人性洞察和独特的叙事风格著称。</p><p>余华的作品多次获得国内外文学大奖，包括意大利格林扎纳·卡佛文学奖、法国文学和艺术骑士勋章等。他的作品被翻译成多种语言，在世界范围内享有盛誉。</p><p>余华擅长用冷静、简洁的笔触描绘生活的残酷和真实，他的作品往往能引发读者对人生、社会、历史的深刻思考。</p>',
                'category_id' => $literatureCategory->id,
                'total_copies' => 10,
                'available_copies' => 10,
                'price' => 28.00,
                'published_at' => '1993-01-01',
            ],
            [
                'title' => '百年孤独',
                'author' => '加西亚·马尔克斯',
                'isbn' => '978-7-111-12346-4',
                'description' => '魔幻现实主义文学代表作',
                'category_id' => $literatureCategory->id,
                'total_copies' => 7,
                'available_copies' => 7,
                'price' => 55.00,
                'published_at' => '1967-01-01',
            ],
            [
                'title' => '平凡的世界',
                'author' => '路遥',
                'isbn' => '978-7-111-12346-5',
                'description' => '中国当代现实主义文学经典',
                'category_id' => $literatureCategory->id,
                'total_copies' => 9,
                'available_copies' => 9,
                'price' => 108.00,
                'published_at' => '1986-01-01',
            ],
            // 历史类
            [
                'title' => '中国通史',
                'author' => '吕思勉',
                'isbn' => '978-7-111-12345-9',
                'description' => '中国历史通史著作',
                'category_id' => $historyCategory->id,
                'total_copies' => 6,
                'available_copies' => 6,
                'price' => 88.00,
                'published_at' => '1940-01-01',
            ],
            [
                'title' => '史记',
                'author' => '司马迁',
                'isbn' => '978-7-111-12346-6',
                'description' => '中国第一部纪传体通史',
                'content_intro' => '<p>《史记》是中国第一部<strong>纪传体通史</strong>，由西汉史学家司马迁所著，记载了从传说中的黄帝到汉武帝太初四年间共3000多年的历史。</p><p>全书共130篇，包括：</p><ul><li><strong>本纪</strong>：12篇，记载历代帝王的事迹</li><li><strong>世家</strong>：30篇，记载诸侯和重要人物的历史</li><li><strong>列传</strong>：70篇，记载各种人物的传记</li><li><strong>表</strong>：10篇，用表格形式记录历史事件</li><li><strong>书</strong>：8篇，记载典章制度</li></ul><p>《史记》不仅是一部历史著作，更是一部文学巨著。司马迁用生动的笔触，塑造了众多栩栩如生的人物形象，开创了<em>纪传体</em>史书的先河，对后世史学产生了深远影响。</p>',
                'author_intro' => '<p><strong>司马迁</strong>（约公元前145年—约公元前86年），字子长，西汉夏阳（今陕西韩城）人，中国历史上最伟大的史学家之一。他继承父职，担任太史令，开始撰写《史记》。</p><p>公元前99年，司马迁因替李陵辩护，触怒汉武帝，被处以宫刑。这一巨大的屈辱和痛苦，反而激发了他完成《史记》的决心。他在《报任安书》中写道："<em>人固有一死，或重于泰山，或轻于鸿毛</em>"，表达了他为完成历史使命而不惜一切的决心。</p><p>司马迁用毕生精力完成了《史记》，这部巨著不仅记录了中国古代的历史，更体现了史学家"不虚美，不隐恶"的史德，成为后世史学的典范。</p>',
                'category_id' => $historyCategory->id,
                'total_copies' => 4,
                'available_copies' => 4,
                'price' => 158.00,
                'published_at' => null, // 公元前91年，无法用标准日期格式表示
            ],
            [
                'title' => '资治通鉴',
                'author' => '司马光',
                'isbn' => '978-7-111-12346-7',
                'description' => '中国古代编年体史书',
                'category_id' => $historyCategory->id,
                'total_copies' => 3,
                'available_copies' => 3,
                'price' => 298.00,
                'published_at' => '1084-01-01',
            ],
            [
                'title' => '明朝那些事儿',
                'author' => '当年明月',
                'isbn' => '978-7-111-12346-9',
                'description' => '通俗易懂的明朝历史',
                'category_id' => $historyCategory->id,
                'total_copies' => 12,
                'available_copies' => 12,
                'price' => 268.00,
                'published_at' => '2006-01-01',
            ],
            [
                'title' => '全球通史',
                'author' => '斯塔夫里阿诺斯',
                'isbn' => '978-7-111-12346-8',
                'description' => '世界历史通史著作',
                'category_id' => $historyCategory->id,
                'total_copies' => 7,
                'available_copies' => 7,
                'price' => 98.00,
                'published_at' => '1970-01-01',
            ],
            [
                'title' => '明朝那些事儿',
                'author' => '当年明月',
                'isbn' => '978-7-111-12346-9',
                'description' => '通俗易懂的明朝历史',
                'category_id' => $historyCategory->id,
                'total_copies' => 12,
                'available_copies' => 12,
                'price' => 268.00,
                'published_at' => '2006-01-01',
            ],
            // 科学类
            [
                'title' => '时间简史',
                'author' => '史蒂芬·霍金',
                'isbn' => '978-7-111-12347-0',
                'description' => '探索宇宙和时间的科普经典',
                'content_intro' => '<p>《时间简史》是物理学家<strong>史蒂芬·霍金</strong>为普通读者撰写的科普巨著，旨在用通俗易懂的语言解释宇宙的奥秘。本书从<em>大爆炸</em>到<em>黑洞</em>，从<em>量子力学</em>到<em>相对论</em>，全面介绍了现代物理学的基本概念。</p><h3>主要章节包括：</h3><ol><li>我们的宇宙图像</li><li>空间和时间</li><li>膨胀的宇宙</li><li>不确定性原理</li><li>基本粒子和自然的力</li><li>黑洞</li><li>时间箭头</li><li>虫洞和时间旅行</li></ol><p>霍金用生动的比喻和清晰的逻辑，让复杂的物理理论变得易于理解。这本书不仅传播了科学知识，更激发了无数人对宇宙的好奇心。</p>',
                'author_intro' => '<p><strong>史蒂芬·霍金</strong>（1942年—2018年），英国著名理论物理学家、宇宙学家，被誉为继爱因斯坦之后最杰出的理论物理学家之一。他主要研究领域是宇宙论和黑洞，提出了<em>霍金辐射</em>理论。</p><p>霍金在21岁时被诊断患有肌萎缩侧索硬化症（ALS），医生预言他只能活两年。然而，他不仅活了下来，还在轮椅上完成了许多重要的科学研究。他的坚韧意志和科学成就，使他成为了科学界的传奇人物。</p><p>除了科学研究，霍金还致力于科学普及，他的《时间简史》被翻译成40多种语言，全球销量超过1000万册，是科学普及领域的里程碑之作。</p>',
                'category_id' => $scienceCategory->id,
                'total_copies' => 9,
                'available_copies' => 9,
                'price' => 45.00,
                'published_at' => '1988-01-01',
            ],
            [
                'title' => '人类简史',
                'author' => '尤瓦尔·赫拉利',
                'isbn' => '978-7-111-12347-1',
                'description' => '从认知革命到科学革命的人类发展史',
                'content_intro' => '<p>《人类简史》是一部宏大的历史著作，从<strong>认知革命</strong>、<strong>农业革命</strong>到<strong>科学革命</strong>，全面梳理了人类从动物到"神"的演化历程。</p><p>作者提出了三个关键问题：</p><ul><li>人类如何从一种不起眼的动物，成为地球的主宰？</li><li>人类如何通过虚构的故事，建立起大规模的合作？</li><li>科学革命如何改变了人类的命运？</li></ul><p>本书不仅讲述了历史事实，更深入探讨了<em>智人</em>如何通过语言、想象力和合作，创造了文明，也带来了战争、不平等和生态危机。这是一部关于人类过去、现在和未来的深刻思考。</p>',
                'author_intro' => '<p><strong>尤瓦尔·赫拉利</strong>（1976年—），以色列历史学家，牛津大学历史学博士，现为耶路撒冷希伯来大学历史系教授。他专注于世界历史和宏观历史进程的研究。</p><p>赫拉利擅长用跨学科的视角审视历史，将历史学、生物学、人类学、心理学等多学科知识融合，提出了许多独到的见解。他的作品以<em>宏大的视野</em>和<em>深刻的思考</em>著称。</p><p>《人类简史》出版后迅速成为全球畅销书，被翻译成50多种语言，销量超过2000万册。赫拉利也因此成为全球知名的历史学家和思想家。</p>',
                'category_id' => $scienceCategory->id,
                'total_copies' => 11,
                'available_copies' => 11,
                'price' => 68.00,
                'published_at' => '2011-01-01',
            ],
            [
                'title' => '物种起源',
                'author' => '查尔斯·达尔文',
                'isbn' => '978-7-111-12347-2',
                'description' => '生物进化论经典著作',
                'category_id' => $scienceCategory->id,
                'total_copies' => 5,
                'available_copies' => 5,
                'price' => 58.00,
                'published_at' => '1859-01-01',
            ],
            [
                'title' => '相对论',
                'author' => '阿尔伯特·爱因斯坦',
                'isbn' => '978-7-111-12347-3',
                'description' => '物理学经典理论',
                'category_id' => $scienceCategory->id,
                'total_copies' => 4,
                'available_copies' => 4,
                'price' => 78.00,
                'published_at' => '1916-01-01',
            ],
            [
                'title' => '量子物理史话',
                'author' => '曹天元',
                'isbn' => '978-7-111-12347-4',
                'description' => '量子物理发展史的通俗读物',
                'category_id' => $scienceCategory->id,
                'total_copies' => 8,
                'available_copies' => 8,
                'price' => 48.00,
                'published_at' => '2006-01-01',
            ],
        ];

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($books as $bookData) {
            // 检查 ISBN 是否已存在
            $existingBook = Book::where('isbn', $bookData['isbn'])->first();
            if ($existingBook) {
                // 如果书籍已存在，检查是否需要更新（特别是新添加的字段）
                $needsUpdate = false;
                $updateData = [];
                
                // 检查是否有新字段需要更新
                if (isset($bookData['content_intro']) && empty($existingBook->content_intro)) {
                    $updateData['content_intro'] = $bookData['content_intro'];
                    $needsUpdate = true;
                }
                if (isset($bookData['author_intro']) && empty($existingBook->author_intro)) {
                    $updateData['author_intro'] = $bookData['author_intro'];
                    $needsUpdate = true;
                }
                
                if ($needsUpdate) {
                    $existingBook->update($updateData);
                    $updated++;
                    $this->command->info("已更新: {$bookData['title']} (ISBN: {$bookData['isbn']})");
                } else {
                    $skipped++;
                    $this->command->warn("跳过已存在的图书: {$bookData['title']} (ISBN: {$bookData['isbn']})");
                }
                continue;
            }

            Book::create($bookData);
            $created++;
            $this->command->info("已创建: {$bookData['title']}");
        }

        $this->command->info("完成！创建了 {$created} 本新图书，更新了 {$updated} 本已存在的图书，跳过了 {$skipped} 本已存在的图书");
    }
}
