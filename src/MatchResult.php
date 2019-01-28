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

    /**
     * @return string|null
     */
    public function result()
    {
        return $this->matches[0] ?? null;
    }

    /**
     * @param string $default
     *
     * @return string
     */
    public function resultOr($default)
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
    public function group($group): string
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
    public function groupOr($group, $default): string
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
    public function namedGroup($group): string
    {
        return $this->group($group);
    }
}
