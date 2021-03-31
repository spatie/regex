<?php

namespace Spatie\Regex;

use Exception;
use Spatie\Regex\Exceptions\RegexFailed;

class ReplaceResult extends RegexResult
{
    public function __construct(
        protected string | array $pattern,
        protected mixed $replacement,
        protected string | array $subject,
        protected string | array $result,
        protected int $count,
    ) {
        //
    }

    public static function for(
        string | array $pattern,
        string | array | callable $replacement,
        string | array $subject,
        int $limit
    ): static {
        try {
            [$result, $count] = ! is_string($replacement) && is_callable($replacement) ?
                static::doReplacementWithCallable($pattern, $replacement, $subject, $limit) :
                static::doReplacement($pattern, $replacement, $subject, $limit);
        } catch (Exception $exception) {
            throw RegexFailed::replace($pattern, $subject, $exception->getMessage());
        }

        if ($result === null) {
            throw RegexFailed::replace($pattern, $subject, static::lastPregError());
        }

        return new static($pattern, $replacement, $subject, $result, $count);
    }

    protected static function doReplacement(
        string | array $pattern,
        string | array | callable $replacement,
        string | array $subject,
        int $limit
    ): array {
        $count = 0;

        $result = preg_replace($pattern, $replacement, $subject, $limit, $count);

        return [$result, $count];
    }

    protected static function doReplacementWithCallable(
        string | array $pattern,
        callable $replacement,
        string | array $subject,
        int $limit
    ): array {
        $replacement = function (array $matches) use ($pattern, $subject, $replacement) {
            return $replacement(new MatchResult($pattern, $subject, true, $matches));
        };

        $count = 0;

        $result = preg_replace_callback($pattern, $replacement, $subject, $limit, $count);

        return [$result, $count];
    }

    public function result(): string | array
    {
        return $this->result;
    }

    public function count(): int
    {
        return $this->count;
    }
}
