<?php

namespace Spatie\Regex\Helpers;

class Str
{
    public static function endsWith(string $haystack, string $needle): bool
    {
        if (strlen($needle) === 0) {
            return true;
        }

        return str_ends_with($haystack, $needle);
    }
}
