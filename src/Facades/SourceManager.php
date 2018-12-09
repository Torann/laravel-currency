<?php

namespace Torann\Currency\Facades;

use Illuminate\Support\Facades\Facade;
use Torann\Currency\SourceManager as Factory;

class SourceManager extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Factory::class;
    }
}
