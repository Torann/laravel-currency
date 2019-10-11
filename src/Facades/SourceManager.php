<?php

namespace Torann\Currency\Facades;

use Illuminate\Support\Facades\Facade;
use Torann\Currency\SourceManager as Factory;

/**
 * @method static update()
 * @method static \Torann\Currency\Contracts\SourceInterface source($name = null)
 * @method static \Torann\Currency\Contracts\SourceInterface driver($name = null)
 * @method static string getDefaultDriver()
 * @method static string fetchesFrom()
 * @method static static fetchFrom($source)
 */
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
