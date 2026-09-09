<?php

declare(strict_types=1);

use Phore\Markdown\Markdown;

require dirname(__DIR__) . '/vendor/autoload.php';

$markdown = <<<'MARKDOWN'
# Project update

#### Implementation details

##### Compatibility note

This is **important**, *compact*, and contains `inline code`.
Read the [documentation](https://example.org/docs).

- First item
- Second item

> A quoted paragraph
> with a second line.
MARKDOWN;

// Raw HTML and unsafe link schemes stay escaped or plain during conversion.
echo Markdown::toHtml($markdown) . PHP_EOL;
