<?php

namespace Better\Regex;
use Exception;

/**
 * Regex
 */
class Regex
{
    /**
     * match
     *
     * @param  mixed $pattern
     * @param  mixed $subject
     * @return MatchResult
     */
    public static function match(string $pattern, string $subject): MatchResult
    {
        return MatchResult::for($pattern, $subject);
    }

    /**
     * matchAll
     *
     * @param  mixed $pattern
     * @param  mixed $subject
     * @return MatchAllResult
     */
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



/**
 * MatchResult
 */
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
     * @return string
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
     * @return string
     * @throws RegexFailed
     */
    public function namedGroup(int | string $group): string
    {
        return $this->group($group);
    }
}



/**
 * MatchAllResult
 */
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
     * @return MatchResult[]
     */
    public function results(): array
    {
        return Arr::map(Arr::transpose($this->matches), function ($match): MatchResult {
            return new MatchResult($this->pattern, $this->subject, true, $match);
        });
    }
}



/**
 * ReplaceResult
 */
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

    /**
     * result
     *
     * @return string
     */
    public function result(): string | array
    {
        return $this->result;
    }
    
    /**
     * count
     *
     * @return int
     */
    public function count(): int
    {
        return $this->count;
    }
}






/**
 * RegexResult
 */
abstract class RegexResult
{
    protected static function lastPregError(): string
    {
        return preg_last_error_msg();
    }
}



/**
 * Arr
 */
class Arr
{
    public static function map(array $array, callable $callback): array
    {
        return array_map($callback, $array);
    }

    public static function transpose(array $array): array
    {
        if (count($array) === 1) {
            $array = static::first($array);

            return array_map(function ($element) {
                return [$element];
            }, $array);
        }

        $numHits = count($array[0]);
        $groups = array_keys($array);
        $result = [];
        for ($hit = 0; $hit < $numHits; $hit++) {
            $group = [];
            foreach ($groups as $groupName) {
                $group[$groupName] = $array[$groupName][$hit];
            }
            $result[] = $group;
        }

        return $result;
    }

    public static function first(array $array): mixed
    {
        return reset($array);
    }
}


/**
 * Str
 */
class Str
{
    public static function endsWith(string $haystack, string $needle): bool
    {
        if (strlen($needle) === 0) {
            return true;
        }

        return str_ends_with($haystack, $needle);
    }
}





/**
 * RegexFailed Exception handling
 */
class RegexFailed extends Exception
{
    public static function match(string $pattern, string $subject, string $message): static
    {
        $subject = static::trimString($subject);

        return new static("Error matching pattern `{$pattern}` with subject `{$subject}`. {$message}");
    }

    public static function replace(string $pattern, string $subject, string $message): static
    {
        $subject = static::trimString($subject);

        return new static("Error replacing pattern `{$pattern}` in subject `{$subject}`. {$message}");
    }

    public static function groupDoesntExist(string $pattern, string $subject, $group): static
    {
        return new static("Pattern `{$pattern}` with subject `{$subject}` didn't capture a group named {$group}");
    }

    protected static function trimString(string $string): string
    {
        if (strlen($string) < 40) {
            return $string;
        }

        return substr($string, 0, 40).'...';
    }
}
