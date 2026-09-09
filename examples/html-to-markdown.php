<?php

declare(strict_types=1);

use Phore\Markdown\Markdown;

require dirname(__DIR__) . '/vendor/autoload.php';

$html = <<<'HTML'
<h1>Project update</h1>
<h4>Implementation details</h4>
<h5>Compatibility note</h5>
<p>This is <strong>important</strong>, <em>compact</em>, and contains <code>inline code</code>.</p>
<p>Read the <a href="https://example.org/docs">documentation</a>.</p>
<ul><li>First item</li><li>Second item</li></ul>
<blockquote><p>A quoted paragraph<br>with a second line.</p></blockquote>
HTML;

// Unsupported tags retain readable child text; active and remote content is removed.
echo Markdown::fromHtml($html) . PHP_EOL;
