<?php

namespace Spatie\Regex\Helpers;

class Str
{
    public static function endsWith(string $haystack, string $needle): bool
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return substr($haystack, -$length) === $needle;
    }
}
