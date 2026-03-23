<?php

namespace App\Providers;

use App\Database\NeonPostgresConnector;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Replace the default pgsql connector with our Neon-aware one.
        // When NEON_ENDPOINT_ID is set, it appends `options=endpoint=<id>`
        // to the DSN so old libpq clients (e.g. XAMPP PHP 7.4) can connect.
        $this->app->bind('db.connector.pgsql', NeonPostgresConnector::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
