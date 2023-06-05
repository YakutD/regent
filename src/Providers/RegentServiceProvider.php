<?php

namespace MultihandED\Regent\Providers;

use Illuminate\Support\ServiceProvider;
use MultihandED\Regent\Builder;
use MultihandED\Regent\Facades\Regent;

class RegentServiceProvider extends ServiceProvider
{
  public function register()
  {
    $this->app->bind(Regent::ACCESSOR, function ($app) 
    {
      return Builder::init();
    });
  }
}
