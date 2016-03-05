<?php

namespace Michaeljoyner\Edible;

use ContentWriter;
use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;
use Symfony\Component\Yaml\Parser;

class EdibleServiceProvider extends ServiceProvider
{

    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if (! $this->app->routesAreCached()) {
            require __DIR__.'/../resources/routes/routes.php';
        }

        $this->publishesMigrations();
        $this->publishesConfig();

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'edible');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/edible'),
        ]);

        if(config('edible.menu_view')) {
            view()->composer(config('menu_view'), function ($view) {
                $ediblePages = (new ContentRepository())->getPageListWithUrls();

                return $view->with(compact('ediblePages'));
            });
        }


    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(['Michaeljoyner\Edible\Commands\MapContentStructure']);
    }

    public function provides()
    {
        return [ContentWriter::class];
    }

    private function publishesMigrations()
    {
        $filesMap = [];
        $resourcePath = __DIR__ . '/../resources/migrations/';
        $files = [
            '2016_01_26_004711_create_edible_pages_table.php',
            '2016_01_26_004750_create_edible_textblocks_table.php',
            '2016_01_26_004801_create_edible_galleries_table.php'
        ];

        foreach ($files as $file) {
            $filesMap[$resourcePath . $file] = database_path('migrations/' . $file);
        }

        $this->publishes($filesMap, 'migrations');
    }

    private function publishesConfig()
    {
        $resourcePath = __DIR__ . '/../resources/config/';
        $this->publishes([$resourcePath . 'edible.php' => config_path('edible.php')], 'config');
    }
}
