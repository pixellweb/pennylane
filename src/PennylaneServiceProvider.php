<?php

namespace PixellWeb\Pennylane;


use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Ipsum\Reservation\app\Contracts\FactureContract;
use PixellWeb\Pennylane\app\Console\Commands\Produit;
use PixellWeb\Pennylane\app\Console\Commands\Test;
use PixellWeb\Pennylane\app\FacturePennylaneProvider;



class PennylaneServiceProvider extends ServiceProvider
{

    protected $commands = [
        Test::class,
        Produit::class,
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

        $this->loadViewsFrom(__DIR__.'/ressources/views', 'Pennylane');

        $this->addCustomConfigurationValues();

        /*Route::middleware(['web'])
            ->prefix(config('ipsum.admin.route_prefix'))
            ->group(__DIR__.'/routes/admin.php');*/
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

        if (config('ipsum.reservation.facture_provider') === 'pennylane') {
            $this->app->bind(FactureContract::class, FacturePennylaneProvider::class);
        }
    }
}
