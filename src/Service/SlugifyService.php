<?php

namespace App\Service;

class SlugifyService
{
    public function slugify($text)
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);

        return $text;
    }
}
