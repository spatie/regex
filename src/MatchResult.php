<?php

namespace Spatie\Regex;

use Exception;

class MatchResult extends RegexResult
{
    /** @var string */
    protected $pattern;

    /** @var string */
    protected $subject;

    /** @var bool */
    protected $hasMatch;

    /** @var array */
    protected $matches;

    public function __construct(string $pattern, string $subject, bool $hasMatch, array $matches)
    {
        $this->pattern = $pattern;
        $this->subject = $subject;
        $this->hasMatch = $hasMatch;
        $this->matches = $matches;
    }

    public static function for(string $pattern, string $subject)
    {
        $matches = [];

        try {
            $result = preg_match($pattern, $subject, $matches);
        } catch (Exception $exception) {
            throw RegexFailed::match($pattern, $subject, $exception->getMessage());
        }

        if ($result === false) {
            throw RegexFailed::match($pattern, $subject, static::lastPregError());
        }

        return new static($pattern, $subject, $result, $matches);
    }

    public function hasMatch(): bool
    {
        return $this->hasMatch;
    }

    public function result()
    {
        return $this->matches[0] ?? null;
    }

    public function group(int $index): string
    {
        if (! isset($this->matches[$index])) {
            throw RegexFailed::groupDoesntExist($this->pattern, $this->subject, $index);
        }

        return $this->matches[$index];
    }
}
