<?php

use MultihandED\Regent\Facades\Regent;
use MultihandED\Regent\Tests\Traits\QuotedTemplateTrait;

uses(QuotedTemplateTrait::class);

test('clearPattern', function()
{
    $regent = Regent::init();
    $regent->pattern = 'testPattern';
    $regent->clearPattern();
    
    $this->assertSame('', $regent->pattern);
});

test('pattern methods with $string and $quote arguments', function()
{
    [$template, $templateQuoted] = $this->getTemplates();

    $methods = [
        'startsWith' => "^$template",
        'endsWith' => "$template$",
        'or' => "|$template",
    ];

    $regent = Regent::init();

    $this->assertSame('', $regent->pattern);

    $regent->addPattern($template);
    $this->assertSame($template, $regent->pattern);

    $regent->clearPattern()->addPattern($template, true);
    $this->assertSame($templateQuoted, $regent->pattern);

    foreach($methods as $method => $result)
    {
        $regent->clearPattern();

        $regent->$method();
        $this->assertSame(str_replace($template, '', $result), $regent->pattern);

        $regent->clearPattern()->$method($template);
        $this->assertSame($result, $regent->pattern);

        $regent->clearPattern()->$method($template, true);
        $this->assertSame(str_replace($template, $templateQuoted, $result), $regent->pattern);
    }
});

test('pattern methods without arguments', function()
{
    $methods = [
        'openGroup' => '(',
        'closeGroup' => ')',
        'startOfLine' => '\A',
        'endOfLine' => '\Z',
        'endOfAction' => '\G',
        'closeAnyOf' => ']',
        'anyCharacter' => '.',
    ];

    $regent = Regent::init();

    foreach($methods as $method => $result)
    {
        $regent->$method();
        $this->assertSame($result, $regent->pattern);

        $regent->clearPattern();
    }
});

test('pattern methods with $except argument', function()
{
    $methods = [
        'alphaNum' => '\w',
        'whiteSpace' => '\s',
        'whiteSpaceVertical' => '\v',
        'whiteSpaceHorizontal' => '\h',
        'digit' => '\d',
        'borderOfWord' => '\b',
    ];

    $regent = Regent::openAnyOf();
    $this->assertSame('[', $regent->pattern);

    $regent->clearPattern();

    $regent->openAnyOf(true);
    $this->assertSame('[^', $regent->pattern);

    foreach($methods as $method => $result)
    {
        $regent->clearPattern();

        $regent->$method();
        $this->assertSame($result, $regent->pattern);

        $regent->clearPattern()->$method(true);
        $this->assertSame(strtoupper($result), $regent->pattern);
    }
});

test('backReference', function()
{
    $regent = Regent::backReference(5);
    $this->assertSame('\\5', $regent->pattern);

    $regent->clearPattern();
});

test('look', function()
{
    [$template, $templateQuoted] = $this->getTemplates();

    $results = [
        '?='  => [false, false],
        '?<=' => [true, false],
        '?<!' => [true, true],
        '?!'  => [false, true],
    ];

    $regent = Regent::init();

    foreach($results as $result => $args)
    {
        [$ahead, $except] = $args;

        $regent->look($ahead, $except);
        $this->assertSame($result, $regent->pattern);

        $regent->clearPattern()->look($ahead, $except, $template);
        $this->assertSame($result . $template, $regent->pattern);

        $regent->clearPattern()->look($ahead, $except, $template, true);
        $this->assertSame($result . $templateQuoted, $regent->pattern);

        $regent->clearPattern();
    }
});

test('range', function()
{
    $from = 'A';
    $to = 10; //? check different types

    $regent = Regent::range($from, $to);
    $this->assertSame("$from-$to", $regent->pattern);
});
