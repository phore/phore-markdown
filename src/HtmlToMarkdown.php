<?php

declare(strict_types=1);

namespace Phore\Markdown;

use DOMDocument;
use DOMNode;

final class HtmlToMarkdown
{
    public static function convert(string $html): string
    {
        if (trim($html) === '') { return ''; }
        $document = self::document($html);
        if ($document === null) { return self::toText($html); }
        self::removeUnsafeNodes($document);
        $body = $document->getElementsByTagName('body')->item(0);
        $markdown = trim($body === null ? self::toText($html) : self::node($body));
        return preg_replace('/\n{3,}/', "\n\n", $markdown) ?? $markdown;
    }

    public static function toText(string $html): string
    {
        $document = self::document($html);
        if ($document === null) {
            return trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }
        self::removeUnsafeNodes($document);
        $body = $document->getElementsByTagName('body')->item(0);
        return trim($body?->textContent ?? '');
    }

    private static function document(string $html): ?DOMDocument
    {
        $document = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $loaded = $document->loadHTML('<?xml encoding="UTF-8"><body>' . $html . '</body>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        return $loaded ? $document : null;
    }

    private static function removeUnsafeNodes(DOMDocument $document): void
    {
        foreach (['script', 'style', 'form', 'iframe', 'img'] as $tag) {
            while (($node = $document->getElementsByTagName($tag)->item(0)) !== null) {
                $node->parentNode?->removeChild($node);
            }
        }
    }

    private static function node(DOMNode $node): string
    {
        if ($node->nodeType === XML_TEXT_NODE) { return $node->nodeValue ?? ''; }
        $content = '';
        foreach ($node->childNodes as $child) { $content .= self::node($child); }
        return match (strtolower($node->nodeName)) {
            'h1' => "\n\n# " . trim($content) . "\n\n",
            'h2' => "\n\n## " . trim($content) . "\n\n",
            'h3' => "\n\n### " . trim($content) . "\n\n",
            'p', 'div' => "\n\n" . trim($content) . "\n\n",
            'br' => "\n",
            'strong', 'b' => '**' . trim($content) . '**',
            'em', 'i' => '*' . trim($content) . '*',
            'code' => '`' . str_replace('`', '\\`', trim($content)) . '`',
            'li' => "\n- " . trim($content),
            'blockquote' => "\n\n" . self::quote(trim($content)) . "\n\n",
            'a' => self::link($node, $content),
            default => $content,
        };
    }

    private static function link(DOMNode $node, string $content): string
    {
        $href = $node->attributes?->getNamedItem('href')?->nodeValue;
        return $href === null || preg_match('/^(https?:|mailto:)/i', $href) !== 1 ? $content : '[' . trim($content) . '](' . $href . ')';
    }

    private static function quote(string $text): string
    {
        return implode("\n", array_map(static fn (string $line): string => '> ' . $line, explode("\n", $text)));
    }
}
