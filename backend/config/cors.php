<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'libbook/api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => array_values(array_unique(array_filter(array_merge(
        [
            'http://localhost:5173',
            'http://127.0.0.1:5173',
            'http://180.76.180.105',
            'https://libbook.yuchuntest.com',
            'https://caoyuchun2003.github.io',
        ],
        env('CORS_ALLOWED_ORIGINS')
            ? array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS')))
            : []
    )))),
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    // Bearer Token 跨域；CFC 代理时由 CFC 处理 CORS
    'supports_credentials' => false,
];
