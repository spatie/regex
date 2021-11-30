<?php

namespace Spatie\Regex;

abstract class RegexResult
{
    protected static function lastPregError(): string
    {
        return preg_last_error_msg();
    }
}
