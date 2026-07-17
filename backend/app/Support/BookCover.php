<?php

namespace App\Support;

class BookCover
{
    private const BLOCKED_HOSTS = [
        'xinsiketang.com',
        'doubanio.com',
        'douban.com',
    ];

    private const PALETTES = [
        ['#1a3a4a', '#2d6a6a'],
        ['#3d2c29', '#8b5a3c'],
        ['#1e3a5f', '#3d6b9a'],
        ['#2c3e2d', '#5a7a4a'],
        ['#4a2c4a', '#7a4a7a'],
        ['#2c2c3e', '#5a5a7a'],
        ['#3e2c1e', '#8a6040'],
        ['#1a3e3e', '#3a7a7a'],
    ];

    public static function isBlocked(?string $url): bool
    {
        if ($url === null || trim($url) === '') {
            return true;
        }

        $lower = strtolower($url);
        foreach (self::BLOCKED_HOSTS as $host) {
            if (str_contains($lower, $host)) {
                return true;
            }
        }

        return false;
    }

    public static function isSafeCustom(?string $url): bool
    {
        if ($url === null || trim($url) === '') {
            return false;
        }

        if (str_starts_with($url, 'data:image/svg')) {
            return true;
        }

        return !self::isBlocked($url);
    }

    /** Resolve API cover: keep safe custom URL, otherwise generate original SVG placeholder. */
    public static function resolve(?string $cover, string $title = '', string $author = '', int|string|null $id = 0): string
    {
        if (self::isSafeCustom($cover) && !str_starts_with((string) $cover, 'data:')) {
            return $cover;
        }

        if ($cover && str_starts_with($cover, 'data:image/svg')) {
            return $cover;
        }

        return self::generate($title, $author, $id);
    }

    public static function generate(string $title = '', string $author = '', int|string|null $id = 0): string
    {
        $seed = abs(crc32((string) $id . '-' . $title . '-' . $author));
        [$c1, $c2] = self::PALETTES[$seed % count(self::PALETTES)];

        $shortTitle = self::truncate($title !== '' ? $title : '宇春书城', 12);
        $shortAuthor = self::truncate($author, 10);
        $initial = mb_substr($shortTitle, 0, 1);

        $svg = sprintf(
            '<?xml version="1.0" encoding="UTF-8"?>'
            . '<svg xmlns="http://www.w3.org/2000/svg" width="300" height="420" viewBox="0 0 300 420">'
            . '<defs><linearGradient id="g" x1="0%%" y1="0%%" x2="100%%" y2="100%%">'
            . '<stop offset="0%%" stop-color="%s"/><stop offset="100%%" stop-color="%s"/>'
            . '</linearGradient></defs>'
            . '<rect width="300" height="420" fill="url(#g)"/>'
            . '<rect x="18" y="18" width="264" height="384" fill="none" stroke="rgba(255,255,255,0.28)" stroke-width="1.5"/>'
            . '<text x="150" y="150" text-anchor="middle" fill="rgba(255,255,255,0.2)" font-size="96" font-family="Georgia, serif">%s</text>'
            . '<text x="150" y="250" text-anchor="middle" fill="#fff" font-size="22" font-family="Georgia, serif">%s</text>'
            . '<text x="150" y="290" text-anchor="middle" fill="rgba(255,255,255,0.75)" font-size="14" font-family="Georgia, serif">%s</text>'
            . '<text x="150" y="380" text-anchor="middle" fill="rgba(255,255,255,0.45)" font-size="12" font-family="Georgia, serif">宇春书城</text>'
            . '</svg>',
            $c1,
            $c2,
            htmlspecialchars($initial, ENT_QUOTES | ENT_XML1, 'UTF-8'),
            htmlspecialchars($shortTitle, ENT_QUOTES | ENT_XML1, 'UTF-8'),
            htmlspecialchars($shortAuthor, ENT_QUOTES | ENT_XML1, 'UTF-8')
        );

        return 'data:image/svg+xml;charset=utf-8,' . rawurlencode($svg);
    }

    private static function truncate(string $text, int $max): string
    {
        if (mb_strlen($text) <= $max) {
            return $text;
        }

        return mb_substr($text, 0, $max);
    }
}
