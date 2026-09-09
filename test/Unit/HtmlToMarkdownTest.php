<?php

declare(strict_types=1);

namespace Phore\Markdown\Test\Unit;

use Phore\Markdown\HtmlToMarkdown;
use PHPUnit\Framework\TestCase;

final class HtmlToMarkdownTest extends TestCase
{
    public function testConvertsCommonHtmlAndRemovesUnsafeNodes(): void
    {
        $html = '<h1>Hello</h1><p>A <strong>bold</strong> <a href="https://example.org">link</a>.</p><script>bad()</script><img src="https://tracker.invalid/pixel">';
        self::assertSame("# Hello\n\nA **bold** [link](https://example.org).", HtmlToMarkdown::convert($html));
        self::assertSame('HelloA bold link.', HtmlToMarkdown::toText($html));
    }

    public function testUnsafeLinksBecomePlainText(): void
    {
        self::assertSame('click', HtmlToMarkdown::convert('<a href="javascript:alert(1)">click</a>'));
    }

    public function testConvertsFourthAndFifthLevelHeadings(): void
    {
        self::assertSame(
            "#### Details\n\n##### Fine print",
            HtmlToMarkdown::convert('<h4>Details</h4><h5>Fine print</h5>'),
        );
    }
}
