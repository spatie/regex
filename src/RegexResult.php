<?php

namespace Spatie\Regex;

use Spatie\Regex\Helpers\Str;

abstract class RegexResult
{
    protected static function lastPregError(): string
    {
        return preg_last_error_msg();
    }
}
