<?php

namespace Michaeljoyner\Edible;

use ContentWriter;
use Illuminate\Support\ServiceProvider;
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
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ContentWriter::class, function ($app) {
            $fileSnapshot = (new ContentSnapshotFactory(new Parser()))->makeSnapshotFromYmlFile(base_path('edible.yml'));
            $databaseSnapshot = (new ContentSnapshotFactory(new Parser()))->makeSnapshotFromRepo(new ContentRepository());

            return new ContentWriter($fileSnapshot, $databaseSnapshot);
        });

        $this->publishes([
            __DIR__ . '/../resources/migrations/2016_01_26_004711_create_pages_table.php' => database_path('migrations/2016_01_26_004711_create_pages_table.php'),
            __DIR__ . '/../resources/migrations/2016_01_26_004750_create_textblocks_table.php' => database_path('migrations/2016_01_26_004750_create_textblocks_table.php'),
        __DIR__ . '/../resources/migrations/2016_01_26_004801_create_galleries_table.php' => database_path('migrations/2016_01_26_004750_create_textblocks_table.php'),
        ], 'migrations');

        $this->commands(['Michaeljoyner\Edible\Console\MapContentStructure']);
    }

    public function provides()
    {
        return [ContentWriter::class];
    }
}
