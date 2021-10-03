<?php

namespace Spatie\Regex;

use Exception;
use Spatie\Regex\Exceptions\RegexFailed;

class MatchResult extends RegexResult
{
    public function __construct(
        protected string $pattern,
        protected string $subject,
        protected bool $hasMatch,
        protected array $matches,
    ) {
        //
    }

    public static function for(string $pattern, string $subject): static
    {
        $matches = [];

        try {
            $result = preg_match($pattern, $subject, $matches, PREG_UNMATCHED_AS_NULL);
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

    public function result(): ?string
    {
        return $this->matches[0] ?? null;
    }

    public function resultOr(string $default): string
    {
        return $this->result() ?? $default;
    }

    /**
     * Match group by index or name.
     *
     * @param int|string $group
     *
     * @return string
     *
     * @throws RegexFailed
     */
    public function group(int | string $group): string
    {
        if (! isset($this->matches[$group])) {
            throw RegexFailed::groupDoesntExist($this->pattern, $this->subject, $group);
        }

        return $this->matches[$group];
    }

    /**
     * Return an array of the matches.
     *
     * @return array
     */
    public function groups(): array
    {
        return $this->matches;
    }

    /**
     * Match group by index or return default value if group doesn't exist.
     *
     * @param int|string $group
     * @param string     $default
     *
     * @return string
     */
    public function groupOr(int | string $group, string $default): string
    {
        try {
            return $this->group($group);
        } catch (RegexFailed $e) {
            return $default;
        }
    }

    /**
     * Match group by index or name.
     *
     * @param int|string $group
     *
     * @return string
     *
     * @throws RegexFailed
     */
    public function namedGroup(int | string $group): string
    {
        return $this->group($group);
    }
}
