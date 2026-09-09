<?php

declare(strict_types=1);

namespace Phore\Markdown\Test\Unit;

use Phore\Markdown\MarkdownToHtml;
use PHPUnit\Framework\TestCase;

final class MarkdownToHtmlTest extends TestCase
{
    public function testConvertsSupportedMarkdown(): void
    {
        $markdown = "# Hello\n\nA **bold** [link](https://example.org).\n\n- one\n- two\n\n> quoted";
        $expected = '<h1>Hello</h1>' . "\n"
            . '<p>A <strong>bold</strong> <a href="https://example.org">link</a>.</p>' . "\n"
            . '<ul><li>one</li><li>two</li></ul>' . "\n"
            . '<blockquote><p>quoted</p></blockquote>';
        self::assertSame($expected, MarkdownToHtml::convert($markdown));
    }

    public function testEscapesRawHtmlAndRejectsUnsafeLinks(): void
    {
        $html = MarkdownToHtml::convert('<script>alert(1)</script> [click](javascript:alert(1))');
        self::assertStringContainsString('&lt;script&gt;', $html);
        self::assertStringNotContainsString('<a ', $html);
    }

    public function testConvertsFourthAndFifthLevelHeadings(): void
    {
        self::assertSame(
            "<h4>Details</h4>\n<h5>Fine print</h5>",
            MarkdownToHtml::convert("#### Details\n##### Fine print"),
        );
    }
}
