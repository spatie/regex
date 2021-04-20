<?php

namespace Spatie\Regex\Helpers;

class Str
{
    public static function endsWith(string $haystack, string $needle): bool
    {
        return str_ends_with($haystack, $needle);
    }
}
