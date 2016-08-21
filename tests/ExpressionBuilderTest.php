<?php

namespace Spatie\Regex\Test;

use Spatie\Regex\Builder\ExpressionBuilder;

class ExpressionBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    function it_builds_character_classes()
    {
        $exp = ExpressionBuilder::create();
        $this->assertEquals('[a-z]', $exp->characterClass('a-z'));
        $this->assertEquals('[a-zA-Z]', $exp->characterClass('a-zA-Z'));
    }

    /** @test */
    function it_builds_ranges()
    {
        $exp = ExpressionBuilder::create();
        $this->assertEquals('a-z', $exp->range('a', 'z'));
        $this->assertEquals('0-9', $exp->range('0', '9'));
    }

    /** @test */
    function it_builds_groups()
    {
        $exp = ExpressionBuilder::create();
        $this->assertEquals('(.*)', $exp->group('.*'));
    }

    /** @test */
    function it_builds_named_groups()
    {
        $exp = ExpressionBuilder::create();
        $this->assertEquals('(?P<mygroup>.*)', $exp->namedGroup('mygroup', '.*'));
    }

    /** @test */
    function it_builds_back_reference_to_numeric_group()
    {
        $exp = ExpressionBuilder::create();
        $this->assertEquals('\1', $exp->backReferenceToNumericGroup(1));
    }

    /** @test */
    function it_builds_back_reference_to_named_group()
    {
        $exp = ExpressionBuilder::create();
        $this->assertEquals('(?P=mygroup)', $exp->backReferenceToNamedGroup('mygroup'));
    }

    /** @test */
    function it_builds_starting_patterns()
    {
        $exp = ExpressionBuilder::create();
        $this->assertEquals('^start', $exp->startsWith('start'));
    }

    /** @test */
    function it_builds_ending_patterns()
    {
        $exp = ExpressionBuilder::create();
        $this->assertEquals('end$', $exp->endsWith('end'));
    }

    /** @test */
    function it_builds_look_behind_patterns()
    {
        $exp = ExpressionBuilder::create();
        $this->assertEquals('(?<=_)', $exp->lookBehind('_'));
    }

    /** @test */
    function it_builds_look_ahead_patterns()
    {
        $exp = ExpressionBuilder::create();
        $this->assertEquals('(?=_)', $exp->lookAhead('_'));
    }

    /** @test */
    function it_builds_look_not_behind_patterns()
    {
        $exp = ExpressionBuilder::create();
        $this->assertEquals('(?<!_)', $exp->lookNotBehind('_'));
    }

    /** @test */
    function it_builds_look_not_ahead_patterns()
    {
        $exp = ExpressionBuilder::create();
        $this->assertEquals('(?!_)', $exp->lookNotAhead('_'));
    }

    /** @test */
    function it_builds_quantifier_zero_or_one_times()
    {
        $exp = ExpressionBuilder::create();
        $this->assertEquals('?', $exp->zeroOrOneTimes());
    }

    /** @test */
    function it_builds_quantifier_zero_or_more_times()
    {
        $exp = ExpressionBuilder::create();
        $this->assertEquals('*', $exp->zeroOrMoreTimes());
    }

    /** @test */
    function it_builds_quantifier_one_or_more_times()
    {
        $exp = ExpressionBuilder::create();
        $this->assertEquals('+', $exp->oneOrMoreTimes());
    }

    /** @test */
    function it_builds_quantifier_possesive_one_or_more_times()
    {
        $exp = ExpressionBuilder::create();
        $this->assertEquals('++', $exp->possesiveOneOrMoreTimes());
    }

    /** @test */
    function it_builds_lazy_quantifier_one_or_more_times()
    {
        $exp = ExpressionBuilder::create();
        $this->assertEquals('+?', $exp->lazyOneOrMoreTimes());
    }

    /** @test */
    function it_builds_quantifier_between_times()
    {
        $exp = ExpressionBuilder::create();
        $this->assertEquals('{1,2}', $exp->betweenTimes(1, 2));
    }

    /** @test */
    function it_builds_quantifier_lazy_between_times()
    {
        $exp = ExpressionBuilder::create();
        $this->assertEquals('{1,2}?', $exp->lazyBetweenTimes(1, 2));
    }

    /** @test */
    function it_builds_comments()
    {
        $exp = ExpressionBuilder::create();
        $this->assertEquals('(?# comment)', $exp->comment('comment'));
        $this->assertEquals('(?# comment (brackets\))', $exp->comment('comment (brackets)'));
    }

    /** @test */
    function it_builds_extended_comments()
    {
        $exp = ExpressionBuilder::create();
        $this->assertEquals('# comment', $exp->extendedComment('comment'));
        $this->assertEquals('# comment (brackets)', $exp->extendedComment('comment (brackets)'));
    }

    /** @test */
    function it_builds_escaped_expressions()
    {
        $exp = ExpressionBuilder::create();
        $this->assertEquals('\*', $exp->escape('*'));
        $this->assertEquals('\\\\', $exp->escape('\\'));
        $this->assertEquals('something', $exp->escape('something'));
        $this->assertEquals('\/', $exp->escape('/', '/'));
    }

    /** @test */
    function it_builds_alternating_values()
    {
        $exp = ExpressionBuilder::create();
        $this->assertEquals('value1', $exp->alternate('value1'));
        $this->assertEquals('value1|value2', $exp->alternate('value1', 'value2'));
    }


    /** @test */
    function it_concats_expression_parts()
    {
        $exp = ExpressionBuilder::create();
        $this->assertEquals('[0-9a-z]*', $exp->concat(
            $exp->characterClass(
                $exp->concat(
                    $exp->range('0', '9'),
                    $exp->range('a', 'z')
                )
            ),
            $exp->zeroOrMoreTimes()
        ));
    }
}
