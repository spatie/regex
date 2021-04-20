<?php

namespace Spatie\Regex\Helpers;

class Str
{
    public static function endsWith(string $haystack, string $needle): bool
    {
        // str_ends_with() will return false for an empty string
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }
        
        return str_ends_with($haystack, $needle);
    }
}
