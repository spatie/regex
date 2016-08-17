<?php

namespace Spatie\Regex\Test;

use PHPUnit_Framework_TestCase;
use Spatie\Regex\MatchResult;
use Spatie\Regex\Regex;

class ReplaceTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    function it_can_replace_a_pattern_with_a_string()
    {
        $this->assertEquals('bbbb', Regex::replace('/a/', 'b', 'aabb')->result());
    }

    /** @test */
    function it_can_replace_a_patterns_with_a_callback()
    {
        $this->assertEquals('ababc', Regex::replace('/a(b)/', function (MatchResult $match) {
            return $match->result() . $match->result();
        }, 'abc')->result());
    }

    /** @test */
    function it_can_replace_an_array_of_patterns_with_a_replacement()
    {
        $this->assertEquals('cccc', Regex::replace(['/a/', '/b/'], 'c', 'aabb')->result());
    }

    /** @test */
    function it_can_replace_an_array_of_patterns_with_an_array()
    {
        $this->assertEquals('ccdd', Regex::replace(['/a/', '/b/'], ['c', 'd'], 'aabb')->result());
    }

    /** @test */
    function it_can_limit_the_amount_of_replacements()
    {
        $this->assertEquals('babb', Regex::replace('/a/', 'b', 'aabb', 1)->result());
    }

    /** @test */
    function it_counts_the_amount_of_replacements()
    {
        $this->assertEquals(2, Regex::replace('/a/', 'b', 'aabb')->count());
    }
}
