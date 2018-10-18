<?php

namespace Spatie\Regex;

abstract class RegexResult
{
    protected static function lastPregError(): string
    {
        return array_search(preg_last_error(), get_defined_constants(true)['pcre'], true);
    }
}
