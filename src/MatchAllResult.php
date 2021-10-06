<?php

namespace Spatie\Regex;

use Exception;
use Spatie\Regex\Exceptions\RegexFailed;
use Spatie\Regex\Helpers\Arr;

class MatchAllResult extends RegexResult
{
    public function __construct(
        protected string $pattern,
        protected string $subject,
        protected bool $result,
        protected array $matches,
    ) {
        //
    }

    public static function for(string $pattern, string $subject): static
    {
        $matches = [];

        try {
            $result = preg_match_all($pattern, $subject, $matches, PREG_UNMATCHED_AS_NULL);
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
        return $this->result;
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
