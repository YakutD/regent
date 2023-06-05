<?php

namespace MultihandED\Regent\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use MultihandED\Regent\Providers\RegentServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            RegentServiceProvider::class,
        ];
    }
}