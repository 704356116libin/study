<?php

namespace App\Providers;

use App\Tools\ProviderTool;
use App\Tools\PstTool;
use Illuminate\Support\ServiceProvider;

class DemoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('providerTool', function ($app) {
            return ProviderTool::getProviderTool();
        });
        $this->app->bind(
            ProviderTool::class,
            PstTool::class
         );
        /*
         * 上下文绑定
         */
        $this->app->when(PhotoController::class)
            ->needs(Filesystem::class)
            ->give(function () {
                return Storage::disk('local');
            });

        $this->app->when(VideoController::class)
            ->needs(Filesystem::class)
            ->give(function () {
                return Storage::disk('s3');
            });
        /*
         * 标记
         */
        $this->app->bind('SpeedReport', function () {
            //
        });

        $this->app->bind('MemoryReport', function () {
            //
        });

        $this->app->tag(['SpeedReport', 'MemoryReport'], 'reports');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
