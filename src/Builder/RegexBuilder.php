<?php

namespace Spatie\Regex\Builder;

use Spatie\Regex\Regex;
use Spatie\Regex\RegexFailed;

/**
 * Class RegexBuilder
 *
 * @package Spatie\Regex\Builder
 */
class RegexBuilder
{

    const DEFAULT_DELIMITER = '/';

    /**
     * @var string
     */
    private $startDelimiter = self::DEFAULT_DELIMITER;

    /**
     * @var string
     */
    private $endDelimiter = self::DEFAULT_DELIMITER;

    /**
     * @var array
     */
    private $modifiers = [];

    /**
     * @var string
     */
    private $startsWith = '';

    /**
     * @var string
     */
    private $endsWith = '';

    /**
     * @var array|string[]
     */
    private $expressions = [];

    /**
     * @var string
     */
    private $delimiter = '';

    /**
     * RegexBuilder constructor.
     *
     * @param array  $parts
     * @param string $delimiter
     * @param array  $modifiers
     */
    public function __construct(array $parts = [], $delimiter = self::DEFAULT_DELIMITER, array $modifiers = [])
    {
        $this->parts = $parts;
        $this->setDelimiter($delimiter);
        $this->addModifiers($modifiers);
    }

    /**
     * @param array  $parts
     * @param string $delimiter
     * @param array  $modifiers
     *
     * @return RegexBuilder
     */
    public static function create(array $parts = [], $delimiter = self::DEFAULT_DELIMITER, array $modifiers = []): self
    {
        return new self($parts, $delimiter, $modifiers);
    }

    /**
     * @param string $part
     *
     * @return RegexBuilder
     */
    public function startsWith(string $part): self
    {
        $this->startsWith = $part;

        return $this;
    }

    /**
     * @param string $part
     *
     * @return RegexBuilder
     */
    public function endsWith(string $part): self
    {
        $this->endsWith = $part;

        return $this;
    }

    /**
     * @param string $expression
     *
     * @return RegexBuilder
     */
    public function addExpression(string $expression) : self
    {
        $this->expressions[] = $expression;

        return $this;
    }

    /**
     * @param string $modifier
     *
     * @return RegexBuilder
     * @throws \Spatie\Regex\RegexFailed
     */
    public function addModifier(string $modifier): self
    {
        if (strlen($modifier) !== 1 || false === strpos(Regex::MODIFIERS_ALL, $modifier)) {
            throw RegexFailed::invalidModifier($modifier);
        }

        if ($this->hasModifier($modifier)) {
            return $this;
        }

        $this->modifiers[] = $modifier;
        return $this;
    }

    /**
     * @param array|string[] $modifiers
     *
     * @return RegexBuilder
     */
    public function addModifiers(array $modifiers): self
    {
        foreach ($modifiers as $modifier) {
            $this->addModifier($modifier);
        }

        return $this;
    }

    /**
     * @return RegexBuilder
     */
    public function isCaseInsensitive(): self
    {
        $this->addModifier(Regex::MODIFIER_CASE_INSENSITIVE);

        return $this;
    }

    /**
     * @return RegexBuilder
     */
    public function isMultiline(): self
    {
        $this->addModifier(Regex::MODIFIER_MULTILINE);

        return $this;
    }

    /**
     * @return RegexBuilder
     */
    public function isFreeSpacing(): self
    {
        $this->addModifier(Regex::MODIFIER_FREE_SPACING_MODE);

        return $this;
    }

    /**
     * @return RegexBuilder
     */
    public function isUnicode(): self
    {
        $this->addModifier(Regex::MODIFIER_UNICODE);

        return $this;
    }

    /**
     * @param string $modifier
     *
     * @return bool
     */
    public function hasModifier(string $modifier): bool
    {
        return in_array($modifier, $this->modifiers);
    }

    /**
     * @param string $modifier
     *
     * @return RegexBuilder
     */
    public function removeModifier(string $modifier): self
    {
        if (!$this->hasModifier($modifier)) {
            return $this;
        }

        $this->modifiers = array_diff($this->modifiers, [$modifier]);

        return $this;
    }

    /**
     * @param string $delimiter
     *
     * @return RegexBuilder
     * @throws \Spatie\Regex\RegexFailed
     */
    public function setDelimiter(string $delimiter): self
    {
        if (strlen($delimiter) !== 1) {
            throw RegexFailed::invalidDelimiter($delimiter);
        }

        $bracketPos = strpos(Regex::DELIMITER_BRACKET_STYLE_START, $delimiter);

        $this->startDelimiter = $delimiter;
        $this->endDelimiter = ($bracketPos !== false) ? Regex::DELIMITER_BRACKET_STYLE_END[$bracketPos] : $delimiter;

        return $this;
    }

    /**
     * @param string $glue
     *
     * @return string
     * @throws RegexFailed
     */
    public function getRegex(string $glue = ''): string
    {
        $parts = $this->expressions;
        array_unshift($parts, $this->startsWith);
        array_push($parts, $this->endsWith);

        $meta = $this->startDelimiter . '%2$s%1$s%2$s' . $this->endDelimiter . implode('', $this->modifiers);
        $expression = implode($glue, array_filter($parts));
        $pattern = sprintf($meta, $expression, $glue);

        // Test the regular expression (which throws a RegexFailed on invalid regex)
        // Todo: custom error?
        Regex::match($pattern, '');

        return $pattern;
    }
}
