<?php

namespace Spatie\Regex;

use Exception;

class RegexFailed extends Exception
{
    public static function match(string $pattern, string $subject, string $message): self
    {
        $subject = static::trimString($subject);

        return new static("Error matching pattern `{$pattern}` with subject `{$subject}`. {$message}");
    }

    public static function replace(string $pattern, string $subject, string $message): self
    {
        $subject = static::trimString($subject);

        return new static("Error replacing pattern `{$pattern}` in subject `{$subject}`. {$message}");
    }

    public static function groupDoesntExist(string $pattern, string $subject, int $index): self
    {
        return new static("Pattern `{$pattern}` with subject `{$subject}` didn't capture a group at index {$index}");
    }

    public static function namedGroupDoesntExist(string $pattern, string $subject, string $group): self
    {
        return new static("Pattern `{$pattern}` with subject `{$subject}` didn't capture a group with name {$group}");
    }

    /**
     * @param string $modifier
     *
     * @return RegexFailed
     */
    public static function invalidModifier(string $modifier): self
    {
        return new static("Invalid delimiter: {$modifier}");
    }

    /**
     * @param string $delimiter
     *
     * @return RegexFailed
     */
    public static function invalidDelimiter(string $delimiter): self
    {
        return new static("Invalid delimiter: {$delimiter}");
    }

    protected static function trimString(string $string): string
    {
        if (strlen($string) < 40) {
            return $string;
        }

        return substr($string, 0, 40).'...';
    }
}
