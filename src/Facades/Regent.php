<?php

namespace MultihandED\Regent\Facades;

use Illuminate\Support\Facades\Facade;

class Regent extends Facade
{
    public const ACCESSOR = 'regent';

    protected static function getFacadeAccessor()
    {
        return self::ACCESSOR;
    }

    protected static function resolveFacadeInstance($name)
    {
        if (isset(static::$resolvedInstance[$name])) 
            return static::$resolvedInstance[$name];
        else if (static::$app)
            return static::$app[$name];
    }
}