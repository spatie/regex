<?php

namespace Spatie\Regex;

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

    public static function for($pattern, $replacement, $subject, $limit)
    {
        $count = 0;

        try {
            if (is_callable($replacement)) {
                $replacement = function (array $matches) use ($pattern, $subject, $replacement) {
                    return $replacement(new MatchResult($pattern, $subject, true, $matches));
                };

                $result = preg_replace_callback($pattern, $replacement, $subject, $limit, $count);
            } else {
                $result = preg_replace($pattern, $replacement, $subject, $limit, $count);
            }
        } catch (Exception $exception) {
            throw RegexFailed::replace($pattern, $subject, $exception->getMessage());
        }

        if ($result === null) {
            throw RegexFailed::replace($pattern, $subject, static::lastPregError());
        }

        return new static($pattern, $replacement, $subject, $result, $count);
    }

    public function result()
    {
        return $this->result;
    }

    public function count()
    {
        return $this->count;
    }
}
