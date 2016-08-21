<?php

namespace Spatie\Regex\Builder;

class ExpressionBuilder
{
    /**
     * @return ExpressionBuilder
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * @param $characters
     *
     * @return string
     */
    public function characterClass($characters): string
    {
        return sprintf('[%s]', $characters);
    }

    /**
     * @param string $from
     * @param string $to
     *
     * @return string
     */
    public function range(string $from, string $to): string
    {
        return sprintf('%s-%s', $from, $to);
    }

    /**
     * @param string $pattern
     *
     * @return string
     */
    public function group(string $pattern): string
    {
        return sprintf('(%s)', $pattern);
    }

    /**
     * @param string $name
     * @param string $pattern
     *
     * @return string
     */
    public function namedGroup(string $name, string $pattern): string
    {
        return $this->group(sprintf('?P<%s>%s', $name, $pattern));
    }

    /**
     * @param int $groupNumber
     *
     * @return string
     */
    public function backReferenceToNumericGroup(int $groupNumber): string
    {
        return sprintf('\%s', $groupNumber);
    }

    /**
     * @param string $groupName
     *
     * @return string
     */
    public function backReferenceToNamedGroup(string $groupName): string
    {
        return sprintf('(?P=%s)', $groupName);
    }

    /**
     * @param string $pattern
     *
     * @return string
     */
    public function startsWith(string $pattern): string
    {
        return sprintf('^%s', $pattern);
    }

    /**
     * @param string $pattern
     *
     * @return string
     */
    public function endsWith(string $pattern): string
    {
        return sprintf('%s$', $pattern);
    }

    /**
     * @param string $pattern
     *
     * @return string
     */
    public function lookBehind(string $pattern): string
    {
        return sprintf('(?<=%s)', $pattern);
    }

    /**
     * @param string $pattern
     *
     * @return string
     */
    public function lookAhead(string $pattern): string
    {
        return sprintf('(?=%s)', $pattern);
    }

    /**
     * @param string $pattern
     *
     * @return string
     */
    public function lookNotBehind(string $pattern): string
    {
        return sprintf('(?<!%s)', $pattern);
    }

    /**
     * @param string $pattern
     *
     * @return string
     */
    public function lookNotAhead(string $pattern): string
    {
        return sprintf('(?!%s)', $pattern);
    }

    /**
     * @return string
     */
    public function zeroOrOneTimes(): string
    {
        return '?';
    }

    /**
     * @return string
     */
    public function zeroOrMoreTimes(): string
    {
        return '*';
    }

    /**
     * @return string
     */
    public function oneOrMoreTimes(): string
    {
        return '+';
    }

    /**
     * @return string
     */
    public function possesiveOneOrMoreTimes(): string
    {
        return '++';
    }

    /**
     * @return string
     */
    public function lazyOneOrMoreTimes(): string
    {
        return $this->oneOrMoreTimes().'?';
    }

    /**
     * @param int $n
     * @param int $m
     *
     * @return string
     */
    public function betweenTimes(int $n, int $m): string
    {
        return sprintf('{%s,%s}', $n, $m);
    }

    /**
     * @param int $n
     * @param int $m
     *
     * @return string
     */
    public function lazyBetweenTimes(int $n, int $m): string
    {
        return $this->betweenTimes($n, $m).'?';
    }

    /**
     * @param string $comment
     *
     * @return string
     */
    public function comment(string $comment): string
    {
        return sprintf('(?# %s)', str_replace(')', '\\)', $comment));
    }

    /**
     * This method requires the free spaces modifier 'x'.
     *
     * @param string $comment
     *
     * @return string
     */
    public function extendedComment(string $comment): string
    {
        return sprintf('# %s', $comment);
    }

    /**
     * @param string      $raw
     * @param string|null $delimiter
     *
     * @return string
     */
    public function escape(string $raw, string $delimiter = null): string
    {
        return preg_quote($raw, $delimiter);
    }

    /**
     * @param array ...$expressions
     *
     * @return string
     */
    public function alternate(...$expressions): string
    {
        return implode('|', $expressions);
    }

    /**
     * @param array ...$expressions
     *
     * @return string
     */
    public function concat(...$expressions): string
    {
        return implode('', $expressions);
    }
}
