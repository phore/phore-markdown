# Phore Markdown

Small, dependency-free converters for common HTML and Markdown. The package intentionally supports a compact subset and falls back to readable text instead of trying to preserve every formatting detail.

```php
use Phore\Markdown\Markdown;

$markdown = Markdown::fromHtml('<h1>Hello</h1><p>A <strong>small</strong> example.</p>');
$html = Markdown::toHtml("# Hello\n\nA **small** example.");
$text = Markdown::textFromHtml('<p>Hello <em>world</em>.</p>');
```

HTML input is treated as untrusted. Scripts, styles, forms, iframes, and images are removed during HTML-to-Markdown or text conversion. Markdown-to-HTML escapes raw HTML and accepts links only for `http`, `https`, and `mailto` URLs.

Supported syntax includes level 1–5 headings, paragraphs, line breaks, emphasis, strong text, inline code, links, unordered lists, and blockquotes.

## Examples

- [`examples/html-to-markdown.php`](examples/html-to-markdown.php) reads HTML elements and emits Markdown.
- [`examples/markdown-to-html.php`](examples/markdown-to-html.php) parses the supported Markdown elements and emits HTML.

Run either script after `composer install`, for example:

```bash
php examples/markdown-to-html.php
```
