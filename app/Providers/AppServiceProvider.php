<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Services\AIService::class);
        $this->app->singleton(\App\Services\ImageService::class);
        $this->app->singleton(\App\Services\ReportService::class);
        $this->app->singleton(\App\Services\ExportService::class);
    }

    public function boot(): void
    {
        //
    }
}
