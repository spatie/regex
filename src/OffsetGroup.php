<?php

namespace Spatie\Regex;

class OffsetGroup
{
    /** @var string */
    protected $content;

    /** @var int */
    protected $offset;

    public function __construct(string $content, int $offset)
    {
        $this->content = $content;
        $this->offset = $offset;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }
}
