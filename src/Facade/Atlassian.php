<?php

namespace Atlassian\Facades;

use Illuminate\Support\Facades\Facade;

class Atlassian extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'atlassian';
    }
}
