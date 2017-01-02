<?php

namespace Spatie\Regex\Test;

use Spatie\Regex\Regex;
use Spatie\Regex\RegexFailed;
use PHPUnit_Framework_TestCase;

class MatchAllTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_can_determine_if_a_match_was_made()
    {
        $this->assertTrue(Regex::matchAll('/a/', 'aaa')->hasMatch());
        $this->assertFalse(Regex::matchAll('/b/', 'aaa')->hasMatch());
    }

    /** @test */
    public function it_can_retrieve_the_matched_results()
    {
        $results = Regex::matchAll('/a/', 'aaa')->results();

        $this->assertCount(3, $results);
        $this->assertEquals('a', $results[0]->result());
        $this->assertEquals('a', $results[1]->result());
        $this->assertEquals('a', $results[2]->result());
    }

    /** @test */
    public function it_returns_an_empty_array_if_a_result_is_queried_for_a_subject_that_didnt_match_a_pattern()
    {
        $this->assertEmpty(Regex::matchAll('/abc/', 'def')->results());
    }

    /** @test */
    public function it_throws_an_exception_if_a_match_throws_an_error()
    {
        $this->expectException(RegexFailed::class);
        $this->expectExceptionMessage(
            RegexFailed::match('/abc', 'abc', 'preg_match_all(): No ending delimiter \'/\' found')->getMessage()
        );

        Regex::matchAll('/abc', 'abc');
    }

    /** @test */
    public function it_throws_an_exception_if_a_match_throws_a_preg_error()
    {
        $this->expectException(RegexFailed::class);
        $this->expectExceptionMessage(
            RegexFailed::match('/(?:\D+|<\d+>)*[!?]/', 'foobar foobar foobar', 'PREG_BACKTRACK_LIMIT_ERROR')->getMessage()
        );

        Regex::matchAll('/(?:\D+|<\d+>)*[!?]/', 'foobar foobar foobar');
    }

    /** @test */
    public function it_can_retrieve_groups_from_the_matched_results()
    {
        $results = Regex::matchAll('/a(b)/', 'abab')->results();

        $this->assertCount(2, $results);
        $this->assertEquals('ab', $results[0]->result());
        $this->assertEquals('b', $results[0]->group(1));
        $this->assertEquals('ab', $results[1]->result());
        $this->assertEquals('b', $results[1]->group(1));
    }
}
