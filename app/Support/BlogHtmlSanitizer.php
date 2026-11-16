<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class BlogHtmlSanitizer
{
    public function clean(?string $html): string
    {
        if (blank($html)) {
            return '';
        }

        if (! str_contains($html, '<')) {
            return nl2br(e($html));
        }

        $config = (new HtmlSanitizerConfig)
            ->allowSafeElements()
            ->allowRelativeLinks()
            ->allowRelativeMedias();

        return (new HtmlSanitizer($config))->sanitize($html);
    }
}