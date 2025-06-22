<?php

namespace Pnph\TaskManagement\Providers;

use Illuminate\Support\ServiceProvider;

class TaskManagementProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');


        $this->publishesMigrations([
            __DIR__ . '/../../database/migrations' => database_path('migrations'),
        ]);

         $this->loadViewsFrom(__DIR__.'/../../resources/views', 'courier');
    }
}
