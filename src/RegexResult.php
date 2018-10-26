<?php

namespace Spatie\Regex;

use Spatie\Regex\Helpers\Str;

abstract class RegexResult
{
    protected static function lastPregError(): string
    {
        $pcreConstants = get_defined_constants(true)['pcre'];
        $pcreErrors = array_filter($pcreConstants, function ($errorMessage) {
            return Str::endsWith($errorMessage, '_ERROR');
        }, ARRAY_FILTER_USE_KEY);

        return array_search(preg_last_error(), $pcreErrors, true);
    }
}
