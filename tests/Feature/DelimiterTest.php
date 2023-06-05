<?php

use MultihandED\Regent\Facades\Regent;

test('set (local and global) and get delimiter', function () 
{
    $delimiterDefault = Regent::getDelimiterDefault();
    $this->assertSame('~', $delimiterDefault);

    $delimiterDefaultNew = '%';
    $delimiterForLocalSet = '#';

    $regent1 = Regent::init();

    Regent::setDelimiterDefault($delimiterDefaultNew);
    $regent2 = Regent::init();

    $regent3 = Regent::init($delimiterForLocalSet);

    $this->assertSame($delimiterDefault, $regent1->getDelimiter());
    $this->assertSame($delimiterDefaultNew, $regent2->getDelimiter());
    $this->assertSame($delimiterForLocalSet, $regent3->getDelimiter());
    
    Regent::setDelimiterDefault($delimiterDefault); //? reset for other tests
});