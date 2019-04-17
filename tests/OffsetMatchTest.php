<?php

namespace Spatie\Regex\Test;

use Spatie\Regex\Regex;
use Spatie\Regex\OffsetGroup;
use Spatie\Regex\RegexFailed;
use PHPUnit\Framework\TestCase;

class OffsetMatchTest extends TestCase
{
    /** @test */
    public function it_can_determine_if_a_match_was_made()
    {
        $this->assertTrue(Regex::matchWithOffset('/abc/', 'abc')->hasMatch());
        $this->assertFalse(Regex::matchWithOffset('/abc/', 'def')->hasMatch());
    }

    /** @test */
    public function it_throws_an_exception_if_a_match_throws_an_error()
    {
        $this->expectException(RegexFailed::class);
        $this->expectExceptionMessage(
            RegexFailed::match('/abc', 'abc', 'preg_match(): No ending delimiter \'/\' found')->getMessage()
        );

        Regex::matchWithOffset('/abc', 'abc');
    }

    /** @test */
    public function it_throws_an_exception_if_a_match_throws_a_preg_error()
    {
        $this->expectException(RegexFailed::class);
        $this->expectExceptionMessage(
            RegexFailed::match('/(?:\D+|<\d+>)*[!?]/', 'foobar foobar foobar', 'PREG_BACKTRACK_LIMIT_ERROR')->getMessage()
        );

        Regex::matchWithOffset('/(?:\D+|<\d+>)*[!?]/', 'foobar foobar foobar');
    }

    /** @test */
    public function it_can_retrieve_the_matched_result()
    {
        $expected = new OffsetGroup('cde', 2);

        $this->assertEquals($expected, Regex::matchWithOffset('/cde/', 'abcdef')->result());
    }

    /** @test */
    public function it_returns_null_if_a_result_is_queried_for_a_subject_that_didnt_match_a_pattern()
    {
        $this->assertNull(Regex::matchWithOffset('/abc/', 'def')->result());
    }

    /** @test */
    public function it_can_retrieve_a_matched_group()
    {
        $expected = new OffsetGroup('b', 1);

        $this->assertEquals($expected, Regex::matchWithOffset('/a(b)c/', 'abcdef')->group(1));
    }

    /** @test */
    public function it_throws_an_exception_if_a_non_existing_group_is_queried()
    {
        $this->expectException(RegexFailed::class);
        $this->expectExceptionMessage(RegexFailed::groupDoesntExist('/(a)bc/', 'abcdef', 2)->getMessage());

        Regex::matchWithOffset('/(a)bc/', 'abcdef')->group(2);
    }

    /** @test */
    public function it_can_retrieve_a_matched_named_group()
    {
        $expected = new OffsetGroup('a', 0);

        $this->assertEquals($expected, Regex::matchWithOffset('/(?<samename>a)bc/', 'abcdef')->namedGroup('samename'));
    }

    /** @test */
    public function it_can_retrieve_all_matched_groups()
    {
        $results = Regex::matchWithOffset('/(c)de/', 'abcdef')->groups();

        $this->assertCount(2, $results);

        $expected0 = new OffsetGroup('cde', 2);
        $this->assertEquals($expected0, $results[0]);

        $expected1 = new OffsetGroup('c', 2);
        $this->assertEquals($expected1, $results[1]);
    }

    /** @test */
    public function it_throws_an_exception_if_a_non_existing_named_group_is_queued()
    {
        $this->expectException(RegexFailed::class);
        $this->expectExceptionMessage(
            RegexFailed::groupDoesntExist('/(?<samename>a)bc/', 'abcdef', 'invalidname')->getMessage()
        );

        Regex::matchWithOffset('/(?<samename>a)bc/', 'abcdef')->namedGroup('invalidname');
    }
}
