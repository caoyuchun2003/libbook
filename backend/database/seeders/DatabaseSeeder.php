<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Book;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 创建管理员用户
        $admin = User::create([
            'name' => '管理员',
            'email' => 'admin@libbook.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // 创建普通用户
        $user = User::create([
            'name' => '测试用户',
            'email' => 'user@libbook.com',
            'password' => Hash::make('user123'),
            'role' => 'user',
        ]);

        // 创建分类
        $categories = [
            ['name' => '计算机', 'description' => '计算机相关书籍'],
            ['name' => '文学', 'description' => '文学作品'],
            ['name' => '历史', 'description' => '历史类书籍'],
            ['name' => '科学', 'description' => '科学类书籍'],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }

        // 创建图书
        $books = [
            // 计算机类
            [
                'title' => 'Vue.js 实战',
                'author' => '张三',
                'isbn' => '978-7-111-12345-6',
                'description' => 'Vue.js 开发指南，从入门到精通',
                'category_id' => 1,
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
                'category_id' => 1,
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
                'category_id' => 1,
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
                'category_id' => 1,
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
                'category_id' => 1,
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
                'category_id' => 1,
                'total_copies' => 5,
                'available_copies' => 5,
                'price' => 128.00,
                'published_at' => '2012-12-01',
            ],
            // 文学类
            [
                'title' => '红楼梦',
                'author' => '曹雪芹',
                'isbn' => '978-7-111-12345-8',
                'description' => '中国古典四大名著之一',
                'category_id' => 2,
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
                'category_id' => 2,
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
                'category_id' => 2,
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
                'category_id' => 2,
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
                'category_id' => 2,
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
                'category_id' => 2,
                'total_copies' => 10,
                'available_copies' => 10,
                'price' => 28.00,
                'published_at' => '1993-01-01',
            ],
            // 历史类
            [
                'title' => '中国通史',
                'author' => '吕思勉',
                'isbn' => '978-7-111-12345-9',
                'description' => '中国历史通史著作',
                'category_id' => 3,
                'total_copies' => 6,
                'available_copies' => 6,
                'price' => 88.00,
                'published_at' => '1940-01-01',
            ],
            [
                'title' => '史记',
                'author' => '司马迁',
                'isbn' => '978-7-111-12346-4',
                'description' => '中国第一部纪传体通史',
                'category_id' => 3,
                'total_copies' => 4,
                'available_copies' => 4,
                'price' => 158.00,
                'published_at' => '-91-01-01',
            ],
            [
                'title' => '资治通鉴',
                'author' => '司马光',
                'isbn' => '978-7-111-12346-5',
                'description' => '中国古代编年体史书',
                'category_id' => 3,
                'total_copies' => 3,
                'available_copies' => 3,
                'price' => 298.00,
                'published_at' => '1084-01-01',
            ],
            [
                'title' => '全球通史',
                'author' => '斯塔夫里阿诺斯',
                'isbn' => '978-7-111-12346-6',
                'description' => '世界历史通史著作',
                'category_id' => 3,
                'total_copies' => 7,
                'available_copies' => 7,
                'price' => 98.00,
                'published_at' => '1970-01-01',
            ],
            // 科学类
            [
                'title' => '时间简史',
                'author' => '史蒂芬·霍金',
                'isbn' => '978-7-111-12346-7',
                'description' => '探索宇宙和时间的科普经典',
                'category_id' => 4,
                'total_copies' => 9,
                'available_copies' => 9,
                'price' => 45.00,
                'published_at' => '1988-01-01',
            ],
            [
                'title' => '人类简史',
                'author' => '尤瓦尔·赫拉利',
                'isbn' => '978-7-111-12346-8',
                'description' => '从认知革命到科学革命的人类发展史',
                'category_id' => 4,
                'total_copies' => 11,
                'available_copies' => 11,
                'price' => 68.00,
                'published_at' => '2011-01-01',
            ],
            [
                'title' => '物种起源',
                'author' => '查尔斯·达尔文',
                'isbn' => '978-7-111-12346-9',
                'description' => '生物进化论经典著作',
                'category_id' => 4,
                'total_copies' => 5,
                'available_copies' => 5,
                'price' => 58.00,
                'published_at' => '1859-01-01',
            ],
            [
                'title' => '相对论',
                'author' => '阿尔伯特·爱因斯坦',
                'isbn' => '978-7-111-12347-0',
                'description' => '物理学经典理论',
                'category_id' => 4,
                'total_copies' => 4,
                'available_copies' => 4,
                'price' => 78.00,
                'published_at' => '1916-01-01',
            ],
        ];

        foreach ($books as $bookData) {
            Book::create($bookData);
        }
    }
}
