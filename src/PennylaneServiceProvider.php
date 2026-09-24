<?php

namespace PixellWeb\Pennylane;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use PixellWeb\Pennylane\app\Console\Commands\Test;


class PennylaneServiceProvider extends ServiceProvider
{

    protected $commands = [
        Test::class
    ];


    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;


    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->addCustomConfigurationValues();

        Route::middleware(['web'])
            ->prefix(config('ipsum.admin.route_prefix'))
            ->group(__DIR__.'/routes/admin.php');
    }

    public function addCustomConfigurationValues()
    {
        // add filesystems.disks for the log viewer
        config([
            'logging.channels.'.config('pennylane.logging_channel') => [
                'driver' => 'single',
                'path' => storage_path('logs/'.config('pennylane.logging_channel').'.log'),
                'level' => 'debug',
            ]
        ]);

    }


    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/pennylane.php', 'pennylane'
        );

        // register the artisan commands
        $this->commands($this->commands);
    }
}
