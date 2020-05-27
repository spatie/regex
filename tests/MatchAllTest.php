<?php

namespace Spatie\Regex\Test;

use PHPUnit\Framework\TestCase;
use Spatie\Regex\Regex;
use Spatie\Regex\RegexFailed;

class MatchAllTest extends TestCase
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

    /** @test */
    public function it_can_match_multiple_named_groups()
    {
        $results = Regex::matchAll('/the sky is (?<color>.+)/', <<<'TEXT'
the sky is blue
foo bar
the sky is green
the sky is red
bar baz
the sky is white
TEXT
        )->results();

        $this->assertCount(4, $results);
        $this->assertEquals('the sky is blue', $results[0]->result());
        $this->assertEquals('blue', $results[0]->group('color'));
        $this->assertEquals('blue', $results[0]->group(1));
        $this->assertEquals('the sky is green', $results[1]->result());
        $this->assertEquals('green', $results[1]->group('color'));
        $this->assertEquals('green', $results[1]->group(1));
        $this->assertEquals('the sky is red', $results[2]->result());
        $this->assertEquals('red', $results[2]->group('color'));
        $this->assertEquals('red', $results[2]->group(1));
        $this->assertEquals('the sky is white', $results[3]->result());
        $this->assertEquals('white', $results[3]->group('color'));
        $this->assertEquals('white', $results[3]->group(1));
    }
}
