<?php

use MultihandED\Regent\Facades\Regent;

test('init', function()
{
    $regent = Regent::init();
    $this->assertInstanceOf(Regent::getFacadeRoot()::class, $regent);
});

test('__toString', function()
{
    $delimiterDefault = Regent::getDelimiterDefault();

    $testPattern = 'testPattern';
    $testFlag = 'testFlag';

    $etalon = "{$delimiterDefault}{$testPattern}{$delimiterDefault}{$testFlag}";

    $regent = Regent::init();
    $regent->pattern = $testPattern;
    $regent->flags = $testFlag;

    $this->assertSame($etalon, (string) $regent);
});

test('clear', function()
{
    $delimiterDefault = Regent::getDelimiterDefault();
    $delimiterLocal = '%';

    for($i = 0; $i <= 1; $i++)
    {
        $withDelimiter = boolval($i);

        $regent = Regent::init($delimiterLocal);
        $regent->flags = 'testFlag';
        $regent->pattern = 'testPattern';
        $regent->clear($withDelimiter);
    
        $this->assertSame('', $regent->flags);
        $this->assertSame('', $regent->pattern);

        $this->assertSame($withDelimiter ? $delimiterDefault : $delimiterLocal, $regent->getDelimiter());
    }
});