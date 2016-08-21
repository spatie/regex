<?php

namespace Spatie\Regex;

class Regex
{
    /**
     * Modifiers.
     * @see http://php.net/manual/en/reference.pcre.pattern.modifiers.php
     */
    const MODIFIERS_ALL = 'imsxuADU';
    const MODIFIER_CASE_INSENSITIVE = 'i';
    const MODIFIER_MULTILINE = 'm';
    const MODIFIER_SINGLE_LINE = 's';
    const MODIFIER_DOTALL = self::MODIFIER_SINGLE_LINE;
    const MODIFIER_UNICODE = 'u';
    const MODIFIER_DOLLAR_END_ONLY = 'z';
    const MODIFIER_FREE_SPACING_MODE = 'x';
    const MODIFIER_PCRE_ANCHORED = 'A';
    const MODIFIER_PCRE_DOLLAR_ENDONLY = 'D';
    const MODIFIER_PCRE_UNGREEDY = 'U';

    /**
     * Delimiters.
     * @see http://php.net/manual/en/regexp.reference.delimiters.php
     */
    const DELIMITER_BRACKET_STYLE_START = '({[<';
    const DELIMITER_BRACKET_STYLE_END = ')}]>';

    /** Meta characters */
    const META_WORD = '\w';
    const META_WHITESPACE = '\s';
    const META_DIGIT = '\d';
    const META_WORD_BOUNDARY = '\b';
    const META_NOT_WORD = '\W';
    const META_NOT_WHITESPACE = '\S';
    const META_NOT_DIGIT = '\D';

    /** Anchor characters */
    const ANCHOR_ABSOLUTE_BEGINNING = '\A';
    const ANCHOR_ABSOLUTE_ENDING = '\z';

    /**
     * @param string $pattern
     * @param string $subject
     *
     * @return \Spatie\Regex\MatchResult
     */
    public static function match(string $pattern, string $subject): MatchResult
    {
        return MatchResult::for($pattern, $subject);
    }

    /**
     * @param string $pattern
     * @param string $subject
     *
     * @return \Spatie\Regex\MatchAllResult
     */
    public static function matchAll(string $pattern, string $subject): MatchAllResult
    {
        return MatchAllResult::for($pattern, $subject);
    }

    /**
     * @param string|array $pattern
     * @param string|array|callable $replacement
     * @param string|array $subject
     * @param int $limit
     *
     * @return \Spatie\Regex\ReplaceResult
     */
    public static function replace($pattern, $replacement, $subject, $limit = -1): ReplaceResult
    {
        return ReplaceResult::for($pattern, $replacement, $subject, $limit);
    }
}
