<?php

namespace Spatie\Regex;

abstract class RegexResult
{
    protected static function lastPregError(): string
    {
        return array_flip(get_defined_constants(true)['pcre'])[preg_last_error()];
    }
}
