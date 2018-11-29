<?php
namespace Pay\Facades;

use Illuminate\Support\Facades\Facade;
use Pay\Pay as PayManager;

/**
 */
class Pay extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return PayManager::class;
    }
}
