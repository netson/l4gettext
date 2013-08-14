<?php namespace Netson\L4gettext\Facades;

use Illuminate\Support\Facades\Facade;

class L4gettext extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'l4gettext'; }
}