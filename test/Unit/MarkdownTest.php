<?php

declare(strict_types=1);

namespace Phore\Markdown\Test\Unit;

use Phore\Markdown\Markdown;
use PHPUnit\Framework\TestCase;

final class MarkdownTest extends TestCase
{
    public function testFacadeExposesBothDirections(): void
    {
        self::assertSame('**Hello**', Markdown::fromHtml('<strong>Hello</strong>'));
        self::assertSame('<p><strong>Hello</strong></p>', Markdown::toHtml('**Hello**'));
        self::assertSame('Hello', Markdown::textFromHtml('<p>Hello</p>'));
    }
}
