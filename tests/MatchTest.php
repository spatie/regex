<?php

namespace Spatie\Regex\Test;

use PHPUnit_Framework_TestCase;
use Spatie\Regex\Regex;
use Spatie\Regex\RegexFailed;

class MatchTest extends PHPUnit_Framework_TestCase
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
    public function it_can_retrieve_a_matched_group_by_name()
    {
        $this->assertEquals('a', Regex::match('/(?P<mygroup>a)bc/', 'abcdef')->namedGroup('mygroup'));
    }

    /** @test */
    public function it_throws_an_exception_if_a_non_existing_named_group_is_queried()
    {
        $this->expectException(RegexFailed::class);
        $this->expectExceptionMessage(RegexFailed::namedGroupDoesntExist('/(?P<mygroup>a)bc/', 'abcdef', 'othergroup')->getMessage());

        Regex::match('/(?P<mygroup>a)bc/', 'abcdef')->namedGroup('othergroup');
    }

}
