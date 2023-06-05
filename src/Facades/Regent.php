<?php

namespace MultihandED\Regent\Facades;

use Illuminate\Support\Facades\Facade;

class Regent extends Facade
{
    public const ACCESSOR = 'regent';

    protected static $cached = false;

    protected static function getFacadeAccessor()
    {
        return self::ACCESSOR;
    }
}