<?php

use MultihandED\Regent\Facades\Regent;
use MultihandED\Regent\Builder;

test('facade is included', function () 
{
    //* Check init via const ACCESSOR
    $regent1 = app(Regent::ACCESSOR);
    $this->assertInstanceOf(Builder::class, $regent1);

    //* Check Facade bind to correct class
    $regent2 = Regent::getFacadeRoot();
    $this->assertInstanceOf(Builder::class, $regent2);
});