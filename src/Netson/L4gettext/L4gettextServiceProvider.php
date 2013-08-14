<?php namespace Netson\L4gettext;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Filesystem\Filesystem;

class L4gettextServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('netson/l4gettext');

        // include custom exceptions
        include_once __DIR__ . '/Exceptions.php';

        // include routes
        include_once __DIR__ . '/../../routes.php';

        // make sure the class is initialized
        $gt = new L4gettext();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // register l4gettext and alias
        $this->registerL4gettext();

        // register blade compiler
        $this->registerL4gettextBladeCompiler();

        // register commands
        $this->registerCompileCommand();
        $this->registerExtractCommand();
        $this->registerListCommand();
    }

    public function registerL4gettext ()
    {
        // register L4gettext
        $this->app['l4gettext'] = $this->app->share(function($app) {
                    return new L4gettext();
                });

        // Shortcut so developers don't need to add an Alias in app/config/app.php
        $this->app->booting(function() {
                    $loader = AliasLoader::getInstance();
                    $loader->alias('L4gettext', 'Netson\L4gettext\Facades\L4gettext');
                });
    }

    public function registerL4gettextBladeCompiler ()
    {
        // register L4gettext
        $this->app['bladecompiler'] = $this->app->share(function($app) {
                    return new Compilers\BladeCompiler(new Filesystem, "");
                });

        // Shortcut so developers don't need to add an Alias in app/config/app.php
        $this->app->booting(function() {
                    $loader = AliasLoader::getInstance();
                    $loader->alias('BladeCompiler', 'Netson\L4gettext\Facades\BladeCompiler');
                });
    }

    public function registerCompileCommand ()
    {
        // add compile command to artisan
        $this->app['l4gettext.compile'] = $this->app->share(function($app) {
                    return new Commands\CompileCommand();
                });
        $this->commands('l4gettext.compile');
    }

    public function registerExtractCommand ()
    {
        // add extract command to artisan
        $this->app['l4gettext.extract'] = $this->app->share(function($app) {
                    return new Commands\ExtractCommand();
                });
        $this->commands('l4gettext.extract');
    }

    public function registerListCommand ()
    {
        // add list command to artisan
        $this->app['l4gettext.list'] = $this->app->share(function($app) {
                    return new Commands\ListCommand();
                });
        $this->commands('l4gettext.list');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array("L4gettext");
    }

}