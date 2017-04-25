<?php

namespace Freyo\Flysystem\QcloudCOSv3;

use Freyo\Flysystem\QcloudCOSv3\Plugins\GetUrl;
use Freyo\Flysystem\QcloudCOSv3\Plugins\PutRemoteFile;
use Freyo\Flysystem\QcloudCOSv3\Plugins\PutRemoteFileAs;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use League\Flysystem\Filesystem;

/**
 * Class ServiceProvider.
 */
class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/filesystems.php' => config_path('filesystems.php'),
        ]);

        Storage::extend('cosv3', function ($app, $config) {
            return new Filesystem(new Adapter($config));
        });

        Storage::disk('cosv3')
                ->addPlugin(new PutRemoteFile())
                ->addPlugin(new PutRemoteFileAs())
                ->addPlugin(new GetUrl());
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/filesystems.php', 'filesystems'
        );
    }
}
