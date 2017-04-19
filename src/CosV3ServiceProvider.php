<?php

namespace Freyo\LaravelQcloudCosV3;

use Freyo\LaravelQcloudCosV3\Plugins\GetUrl;
use Freyo\LaravelQcloudCosV3\Plugins\PutRemoteFile;
use Freyo\LaravelQcloudCosV3\Plugins\PutRemoteFileAs;
use Freyo\LaravelQcloudCosV3\CosV3Adapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

class CosV3ServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Storage::extend('cosv3', function ($app, $config) {
            return new Filesystem(new CosV3Adapter($config));
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
        $source = realpath(__DIR__ . '/config.php');
        
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $source => config_path('filesystems.php'),
            ]);
        }
        
        $this->mergeConfigFrom($source, 'filesystems.disks');  
    }
}
