<?php

namespace Spatie\Regex\Test;

use Spatie\Regex\Regex;
use Spatie\Regex\MatchResult;
use PHPUnit\Framework\TestCase;

class ReplaceTest extends TestCase
{
    /** @test */
    public function it_can_replace_a_pattern_with_a_string()
    {
        $this->assertEquals('bbbb', Regex::replace('/a/', 'b', 'aabb')->result());
    }

    /** @test */
    public function it_throws_exception_on_invalid_pattern()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error replacing pattern `/a` in subject `aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa...`. preg_replace(): No ending delimiter \'/\' found');

        Regex::replace('/a', 'b', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa')->result();
    }

    /** @test */
    public function it_can_replace_a_patterns_with_a_callback()
    {
        $this->assertEquals('ababc', Regex::replace('/a(b)/', function (MatchResult $match) {
            return $match->result().$match->result();
        }, 'abc')->result());
    }

    /** @test */
    public function it_can_use_an_existing_function_name_as_replacement_string()
    {
        $this->assertEquals('_b_b', Regex::replace('/a/', '_', 'abab')->result());
    }

    /** @test */
    public function it_can_replace_an_array_of_patterns_with_a_replacement()
    {
        $this->assertEquals('cccc', Regex::replace(['/a/', '/b/'], 'c', 'aabb')->result());
    }

    /** @test */
    public function it_can_replace_an_array_of_patterns_with_an_array()
    {
        $this->assertEquals('ccdd', Regex::replace(['/a/', '/b/'], ['c', 'd'], 'aabb')->result());
    }

    /** @test */
    public function it_can_limit_the_amount_of_replacements()
    {
        $this->assertEquals('babb', Regex::replace('/a/', 'b', 'aabb', 1)->result());
    }

    /** @test */
    public function it_counts_the_amount_of_replacements()
    {
        $this->assertEquals(2, Regex::replace('/a/', 'b', 'aabb')->count());
    }
}
