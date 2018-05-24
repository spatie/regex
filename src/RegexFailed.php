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

    public static function groupDoesntExist(string $pattern, string $subject, $group): self
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
