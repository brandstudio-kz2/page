<?php
namespace BrandStudio\Page;

use Illuminate\Support\ServiceProvider;

class PageServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/page.php', 'page');

        if ($this->app->runningInConsole()) {
            $this->publish();
        }

        if (config('page.use_backpack')) {
            $this->loadRoutesFrom(__DIR__.'/routes/page.php');
        }

        $this->app->bind('brandstudio_templatemanager',function() {
            return new TemplateManager(config('page'));
        });

    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'brandstudio');
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'page');

        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/database/migrations');
            $this->publish();
        }
    }

    private function publish()
    {
        $this->publishes([
            __DIR__.'/config/page.php' => config_path('page.php')
        ], 'config');

        $this->publishes([
            __DIR__.'/database/migrations/' => database_path('migrations')
        ], 'migrations');

        $this->publishes([
            __DIR__.'/resources/views/page' => resource_path('views/vendor/brandstudio/page')
        ], 'views');

        $this->publishes([
            __DIR__.'/resources/lang' => resource_path('lang/vendor/page')
        ], 'lang');
    }

}
