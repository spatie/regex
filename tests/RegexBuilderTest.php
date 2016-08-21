<?php

namespace Spatie\Regex\Test;

use Spatie\Regex\Builder\ExpressionBuilder;
use Spatie\Regex\Builder\RegexBuilder;
use Spatie\Regex\Regex;
use Spatie\Regex\RegexFailed;

/**
 * Class RegexBuilderTest
 *
 * @package Spatie\Regex\Test
 */
class RegexBuilderTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    function it_builds_a_regex()
    {
        $builder = RegexBuilder::create();
        $this->assertEquals('//', $builder->getRegex());
    }

    /** @test */
    function it_throws_an_exception_on_invalid_regex()
    {
        $builder = RegexBuilder::create();
        $builder->addExpression('InvalidRegular)Expression');
        $this->expectException(RegexFailed::class);
        $this->assertEquals('//', $builder->getRegex());
    }

    /** @test */
    function it_can_add_expression_parts()
    {
        $builder = RegexBuilder::create();
        $builder->addExpression('(otherpart)');
        $this->assertEquals('/(otherpart)/', $builder->getRegex());
    }

    /** @test */
    function it_can_anchor_starting_expressions()
    {
        $builder = RegexBuilder::create();
        $builder->startsWith('^start');
        $builder->addExpression('(otherpart)');
        $this->assertEquals('/^start(otherpart)/', $builder->getRegex());
    }

    /** @test */
    function it_can_anchor_ending_expressions()
    {
        $builder = RegexBuilder::create();
        $builder->endsWith('end$');
        $builder->addExpression('(otherpart)');
        $this->assertEquals('/(otherpart)end$/', $builder->getRegex());
    }

    /** @test */
    function it_can_anchor_both_start_and_ending_expressions()
    {
        $builder = RegexBuilder::create();
        $builder->startsWith('^start');
        $builder->endsWith('end$');
        $builder->addExpression('(otherpart)');
        $this->assertEquals('/^start(otherpart)end$/', $builder->getRegex());
    }

    /** @test */
    function it_can_add_a_modifier()
    {
        $builder = RegexBuilder::create();
        $builder->addModifier(Regex::MODIFIER_MULTILINE);
        $this->assertEquals('//m', $builder->getRegex());
    }

    /** @test */
    function it_knows_modifier_is_added()
    {
        $builder = RegexBuilder::create();
        $builder->addModifier(Regex::MODIFIER_MULTILINE);
        $this->assertTrue($builder->hasModifier(Regex::MODIFIER_MULTILINE));
        $this->assertFalse($builder->hasModifier(Regex::MODIFIER_SINGLE_LINE));
    }

    /** @test */
    function it_can_not_add_multi_char_modifier()
    {
        $builder = RegexBuilder::create();
        $this->expectException(RegexFailed::class);
        $builder->addModifier('invalid');
    }

    /** @test */
    function it_can_not_add_unsupported_modifier()
    {
        $builder = RegexBuilder::create();
        $this->expectException(RegexFailed::class);
        $builder->addModifier('z');
    }

    /** @test */
    function it_can_add_multiple_modifiers()
    {
        $builder = RegexBuilder::create();
        $builder->addModifiers([Regex::MODIFIER_MULTILINE, Regex::MODIFIER_FREE_SPACING_MODE]);
        $this->assertEquals('//mx', $builder->getRegex());
    }

    /** @test */
    function it_can_remove_modifiers()
    {
        $builder = RegexBuilder::create();
        $builder->addModifier(Regex::MODIFIER_MULTILINE);
        $builder->removeModifier(Regex::MODIFIER_MULTILINE);
        $this->assertEquals('//', $builder->getRegex());
    }

    /** @test */
    function it_can_mark_as_case_insensitive()
    {
        $builder = RegexBuilder::create();
        $builder->isCaseInsensitive();
        $this->assertEquals('//i', $builder->getRegex());
    }

    /** @test */
    function it_can_mark_as_multiline()
    {
        $builder = RegexBuilder::create();
        $builder->isMultiline();
        $this->assertEquals('//m', $builder->getRegex());
    }

    /** @test */
    function it_can_mark_as_unicode()
    {
        $builder = RegexBuilder::create();
        $builder->isUnicode();
        $this->assertEquals('//u', $builder->getRegex());
    }

    /** @test */
    function it_can_mark_as_free_spacing()
    {
        $builder = RegexBuilder::create();
        $builder->isFreeSpacing();
        $this->assertEquals('//x', $builder->getRegex());
    }

    /** @test */
    function it_can_set_a_delimiter()
    {
        $builder = RegexBuilder::create();
        $builder->setDelimiter('#');
        $this->assertEquals('##', $builder->getRegex());
    }

    /** @test */
    function it_can_use_bracket_delimiters()
    {
        $builder = RegexBuilder::create();

        $builder->setDelimiter('{');
        $this->assertEquals('{}', $builder->getRegex());

        $builder->setDelimiter('[');
        $this->assertEquals('[]', $builder->getRegex());

        $builder->setDelimiter('<');
        $this->assertEquals('<>', $builder->getRegex());

        $builder->setDelimiter('(');
        $this->assertEquals('()', $builder->getRegex());
    }

    /** @test */
    function it_can_not_set_an_multi_character_delimiter()
    {
        $this->expectException(RegexFailed::class);

        $builder = RegexBuilder::create();
        $builder->setDelimiter('##');
    }

    /** @test */
    function it_can_output_multiline_regexes()
    {
        $builder = RegexBuilder::create();
        $expr = ExpressionBuilder::create();
        $builder
            ->isFreeSpacing()
            ->addExpression($expr->extendedComment('This one matches everything'))
            ->addExpression($expr->group('.*'))
        ;

        $expected = <<<EOF
/
# This one matches everything
(.*)
/x
EOF;

        $this->assertEquals($expected, $builder->getRegex("\n"));
    }
}
