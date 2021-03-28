<?php

namespace Spatie\Regex;

class Regex
{
    public static function match(string $pattern, string $subject): MatchResult
    {
        return MatchResult::for($pattern, $subject);
    }

    public static function matchAll(string $pattern, string $subject): MatchAllResult
    {
        return MatchAllResult::for($pattern, $subject);
    }

    public static function replace(
        string | array $pattern,
        string | array | callable $replacement,
        string | array $subject,
        int $limit = -1
    ): ReplaceResult {
        return ReplaceResult::for($pattern, $replacement, $subject, $limit);
    }
}
