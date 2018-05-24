<?php

namespace Spatie\Regex;

use Exception;
use Spatie\Regex\Helpers\Arr;

class MatchAllResult extends RegexResult
{
    /** @var string */
    protected $pattern;

    /** @var string */
    protected $subject;

    /** @var bool */
    protected $hasMatch;

    /** @var array */
    protected $matches;

    public function __construct(string $pattern, string $subject, bool $result, array $matches)
    {
        $this->pattern = $pattern;
        $this->subject = $subject;
        $this->hasMatch = $result;
        $this->matches = $matches;
    }

    /**
     * @param string $pattern
     * @param string $subject
     *
     * @return static
     *
     * @throws \Spatie\Regex\RegexFailed
     */
    public static function for(string $pattern, string $subject)
    {
        $matches = [];

        try {
            $result = preg_match_all($pattern, $subject, $matches);
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

    /**
     * @return \Spatie\Regex\MatchResult[]
     */
    public function results(): array
    {
        return Arr::map(Arr::transpose($this->matches), function ($match): MatchResult {
            return new MatchResult($this->pattern, $this->subject, true, $match);
        });
    }
}
