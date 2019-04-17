<?php

namespace Spatie\Regex;

use Exception;

class OffsetMatchResult extends RegexResult
{
    /** @var string */
    protected $pattern;

    /** @var string */
    protected $subject;

    /** @var bool */
    protected $hasMatch;

    /** @var OffsetGroup[] */
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
            $result = preg_match($pattern, $subject, $matches, PREG_OFFSET_CAPTURE);
        } catch (Exception $exception) {
            throw RegexFailed::match($pattern, $subject, $exception->getMessage());
        }

        if ($result === false) {
            throw RegexFailed::match($pattern, $subject, static::lastPregError());
        }

        $realMatches = array_filter($matches, function (array $group) {
            return $group[1] !== -1;
        });

        $offsetGroups = array_map(function ($group) {
            return new OffsetGroup($group[0], $group[1]);
        }, $realMatches);

        return new static($pattern, $subject, $result, $offsetGroups);
    }

    public function hasMatch(): bool
    {
        return $this->hasMatch;
    }

    /**
     * @return OffsetGroup|null
     */
    public function result()
    {
        return $this->matches[0] ?? null;
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
    public function group($group): OffsetGroup
    {
        if (! isset($this->matches[$group])) {
            throw RegexFailed::groupDoesntExist($this->pattern, $this->subject, $group);
        }

        return $this->matches[$group];
    }

    /**
     * Return an array of the matches.
     *
     * @return OffsetGroup[]
     */
    public function groups(): array
    {
        return $this->matches;
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
    public function namedGroup($group): OffsetGroup
    {
        return $this->group($group);
    }
}
