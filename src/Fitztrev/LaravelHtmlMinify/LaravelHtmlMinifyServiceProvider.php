<?php namespace Fitztrev\LaravelHtmlMinify;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Engines\CompilerEngine;

class LaravelHtmlMinifyServiceProvider extends ServiceProvider
{
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
        $this->package('fitztrev/laravel-html-minify');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;
        $app->view->getEngineResolver()->register('blade.php',
            function () use ($app) {
                $cachePath = $app['path'].'/storage/views';
                $compiler  = new LaravelHtmlMinifyCompiler(
                    $app->make('config')->get('laravel-html-minify::config'),
                    $app['files'],
                    $cachePath
                );

                return new CompilerEngine($compiler);
            });
        $app->view->addExtension('blade.php', 'blade.php');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

}
