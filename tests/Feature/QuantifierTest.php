<?php

use MultihandED\Regent\Facades\Regent;
use MultihandED\Regent\Tests\Traits\QuotedTemplateTrait;

uses(QuotedTemplateTrait::class);

test('quantifier methods with $lazy, $string and $quote arguments', function()
{
    [$template, $templateQuoted] = $this->getTemplates();

    $methods = [
        'zeroOrMore' => '*',
        'oneOrMore'  => '+',
        'zeroOrOne'  => '?',
    ];

    $regent = Regent::init();

    foreach($methods as $method => $result)
    {
        for($lazy = 0; $lazy <= 1; $lazy++)
        {
            if($lazy)
                $result .= '?';

            $regent->$method($lazy);
            $this->assertSame($result, $regent->pattern);
    
            $regent->clearPattern()->$method($lazy, $template);
            $this->assertSame($template.$result, $regent->pattern);
    
            $regent->clearPattern()->$method($lazy, $template, true);
            $this->assertSame($templateQuoted.$result, $regent->pattern);

            $regent->clearPattern();
        }
    }
});

test('quantifier methods with $lazy and $num arguments', function()
{
    $num = 5;

    $methods = [
        'exactly' => '{'.$num.'}',
        'atLeast' => '{'.$num.',}',
        'atMax' => '{,'.$num.'}',
    ];

    $regent = Regent::init();

    foreach($methods as $method => $result)
    {
        $regent->$method($num);
        $this->assertSame($result, $regent->pattern);

        $regent->clearPattern()->$method($num, true);
        $this->assertSame("$result?", $regent->pattern);

        $regent->clearPattern();
    }
});

test('between', function()
{
    $min = 0;
    $max = 10;

    $result = '{' . $min . ',' . $max . '}';

    $regent = Regent::between($min, $max);
    $this->assertSame($result, $regent->pattern);

    $regent->clearPattern()->between($min, $max, true);
    $this->assertSame("$result?", $regent->pattern);
});