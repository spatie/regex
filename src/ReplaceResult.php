<?php

namespace Spatie\Regex;

use Exception;

class ReplaceResult extends RegexResult
{
    /** @var string|array */
    protected $pattern;

    /** @var string|array */
    protected $replacement;

    /** @var string|array */
    protected $subject;

    /** @var string|array */
    protected $result;

    /** @var int */
    protected $count;

    public function __construct($pattern, $replacement, $subject, $result, int $count)
    {
        $this->pattern = $pattern;
        $this->replacement = $replacement;
        $this->subject = $subject;
        $this->result = $result;
        $this->count = $count;
    }

    /**
     * @param string|array $pattern
     * @param string|array|callable $replacement
     * @param string|array $subject
     * @param int $limit
     *
     * @return \Spatie\Regex\ReplaceResult
     */
    public static function for($pattern, $replacement, $subject, $limit)
    {
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

    protected static function doReplacement($pattern, $replacement, $subject, $limit): array
    {
        $count = 0;

        $result = preg_replace($pattern, $replacement, $subject, $limit, $count);

        return [$result, $count];
    }

    protected static function doReplacementWithCallable($pattern, callable $replacement, $subject, $limit): array
    {
        $replacement = function (array $matches) use ($pattern, $subject, $replacement) {
            return $replacement(new MatchResult($pattern, $subject, true, $matches));
        };

        $count = 0;

        $result = preg_replace_callback($pattern, $replacement, $subject, $limit, $count);

        return [$result, $count];
    }

    /**
     * @return string|array
     */
    public function result()
    {
        return $this->result;
    }

    public function count(): int
    {
        return $this->count;
    }
}
