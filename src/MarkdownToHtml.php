<?php

declare(strict_types=1);

namespace Phore\Markdown;

final class MarkdownToHtml
{
    public static function convert(string $markdown): string
    {
        $lines = preg_split('/\R/u', str_replace(["\r\n", "\r"], "\n", $markdown)) ?: [];
        $html = [];
        $paragraph = [];
        $list = [];
        $quote = [];

        $flushParagraph = static function () use (&$paragraph, &$html): void {
            if ($paragraph !== []) { $html[] = '<p>' . self::inline(implode("\n", $paragraph), true) . '</p>'; $paragraph = []; }
        };
        $flushList = static function () use (&$list, &$html): void {
            if ($list !== []) { $html[] = '<ul>' . implode('', array_map(static fn (string $item): string => '<li>' . self::inline($item) . '</li>', $list)) . '</ul>'; $list = []; }
        };
        $flushQuote = static function () use (&$quote, &$html): void {
            if ($quote !== []) { $html[] = '<blockquote><p>' . self::inline(implode("\n", $quote), true) . '</p></blockquote>'; $quote = []; }
        };
        $flushAll = static function () use ($flushParagraph, $flushList, $flushQuote): void { $flushParagraph(); $flushList(); $flushQuote(); };

        // Block parsing deliberately recognizes only the small documented subset. Everything
        // else becomes escaped paragraph text, which is the safe and readable fallback.
        foreach ($lines as $line) {
            if (trim($line) === '') { $flushAll(); continue; }
            if (preg_match('/^(#{1,5})\s+(.+)$/', $line, $match) === 1) {
                $flushAll();
                $level = strlen($match[1]);
                $html[] = sprintf('<h%d>%s</h%d>', $level, self::inline($match[2]), $level);
                continue;
            }
            if (preg_match('/^[-*]\s+(.+)$/', $line, $match) === 1) {
                $flushParagraph(); $flushQuote(); $list[] = $match[1]; continue;
            }
            if (preg_match('/^>\s?(.*)$/', $line, $match) === 1) {
                $flushParagraph(); $flushList(); $quote[] = $match[1]; continue;
            }
            $flushList(); $flushQuote(); $paragraph[] = $line;
        }
        $flushAll();
        return implode("\n", $html);
    }

    private static function inline(string $text, bool $lineBreaks = false): string
    {
        // Escape before adding the small set of generated tags so raw HTML stays inert.
        $escaped = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
        $escaped = preg_replace_callback('/\[([^\]]+)]\(([^)\s]+)\)/', static function (array $match): string {
            $url = html_entity_decode($match[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if (preg_match('/^(https?:|mailto:)/i', $url) !== 1) { return $match[0]; }
            return '<a href="' . htmlspecialchars($url, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8') . '">' . $match[1] . '</a>';
        }, $escaped) ?? $escaped;
        $escaped = preg_replace('/`([^`]+)`/', '<code>$1</code>', $escaped) ?? $escaped;
        $escaped = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $escaped) ?? $escaped;
        $escaped = preg_replace('/(?<!\*)\*([^*]+)\*(?!\*)/', '<em>$1</em>', $escaped) ?? $escaped;
        return $lineBreaks ? str_replace("\n", '<br>', $escaped) : $escaped;
    }
}
