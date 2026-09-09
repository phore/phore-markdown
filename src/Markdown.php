<?php

declare(strict_types=1);

namespace Phore\Markdown;

final class Markdown
{
    public static function fromHtml(string $html): string { return HtmlToMarkdown::convert($html); }
    public static function toHtml(string $markdown): string { return MarkdownToHtml::convert($markdown); }
    public static function textFromHtml(string $html): string { return HtmlToMarkdown::toText($html); }
}
