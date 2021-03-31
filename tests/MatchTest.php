<?php

namespace Spatie\Regex\Test;

use PHPUnit\Framework\TestCase;
use Spatie\Regex\Exceptions\RegexFailed;
use Spatie\Regex\Regex;

class MatchTest extends TestCase
{
    /** @test */
    public function it_can_determine_if_a_match_was_made()
    {
        $this->assertTrue(Regex::match('/abc/', 'abc')->hasMatch());
        $this->assertFalse(Regex::match('/abc/', 'def')->hasMatch());
    }

    /** @test */
    public function it_throws_an_exception_if_a_match_throws_an_error()
    {
        $this->expectException(RegexFailed::class);
        $this->expectExceptionMessage(
            RegexFailed::match('/abc', 'abc', 'preg_match(): No ending delimiter \'/\' found')->getMessage()
        );

        Regex::match('/abc', 'abc');
    }

    /** @test */
    public function it_throws_an_exception_if_a_match_throws_a_preg_error()
    {
        $this->expectException(RegexFailed::class);
        $this->expectExceptionMessage(
            RegexFailed::match('/(?:\D+|<\d+>)*[!?]/', 'foobar foobar foobar', 'PREG_BACKTRACK_LIMIT_ERROR')->getMessage()
        );

        Regex::match('/(?:\D+|<\d+>)*[!?]/', 'foobar foobar foobar');
    }

    /** @test */
    public function it_can_retrieve_the_matched_result()
    {
        $this->assertEquals('abc', Regex::match('/abc/', 'abcdef')->result());
    }

    /** @test */
    public function it_returns_null_if_a_result_is_queried_for_a_subject_that_didnt_match_a_pattern()
    {
        $this->assertNull(Regex::match('/abc/', 'def')->result());
    }

    /** @test */
    public function it_can_retrieve_a_matched_group()
    {
        $this->assertEquals('a', Regex::match('/(a)bc/', 'abcdef')->group(1));
    }

    /** @test */
    public function it_throws_an_exception_if_a_non_existing_group_is_queried()
    {
        $this->expectException(RegexFailed::class);
        $this->expectExceptionMessage(RegexFailed::groupDoesntExist('/(a)bc/', 'abcdef', 2)->getMessage());

        Regex::match('/(a)bc/', 'abcdef')->group(2);
    }

    /** @test */
    public function it_can_retrieve_a_matched_named_group()
    {
        $this->assertSame('a', Regex::match('/(?<samename>a)bc/', 'abcdef')->namedGroup('samename'));
    }

    /** @test */
    public function it_can_retrieve_all_matched_groups()
    {
        $results = Regex::match('/(a)bc/', 'abcdef')->groups();

        $this->assertCount(2, $results);
        $this->assertEquals('abc', $results[0]);
        $this->assertEquals('a', $results[1]);
    }

    /** @test */
    public function it_throws_an_exception_if_a_non_existing_named_group_is_queued()
    {
        $this->expectException(RegexFailed::class);
        $this->expectExceptionMessage(
            RegexFailed::groupDoesntExist('/(?<samename>a)bc/', 'abcdef', 'invalidname')->getMessage()
        );

        Regex::match('/(?<samename>a)bc/', 'abcdef')->namedGroup('invalidname');
    }

    /** @test */
    public function it_returns_matched_value_even_if_there_is_default()
    {
        $value = Regex::match('/blue/', 'blue')->resultOr('black');

        $this->assertSame('blue', $value);
    }

    /** @test */
    public function it_returns_default_value_if_there_is_no_match()
    {
        $value = Regex::match('/blue/', 'yellow')->resultOr('black');

        $this->assertSame('black', $value);
    }

    /** @test */
    public function it_returns_matched_group_value_even_if_there_is_default()
    {
        $value = Regex::match('/the sky is (.+)/', 'the sky is orange')->groupOr(1, 'blue');

        $this->assertSame('orange', $value);
    }

    /** @test */
    public function it_returns_default_value_if_there_is_no_group()
    {
        $value = Regex::match('/the sky is (.+)/', 'abc')->groupOr(1, 'blue');

        $this->assertSame('blue', $value);
    }
}
