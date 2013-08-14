<?php namespace Netson\L4gettext\Facades;

use Illuminate\Support\Facades\Facade;

class BladeCompiler extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'bladecompiler'; }
}

?>