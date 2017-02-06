<?php

namespace Superbalist\Storage;

use Config;
use Illuminate\Support\ServiceProvider;

class StorageServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->bind('storage', function () {
            $adapter = StorageAdapterFactory::makeCached($this->app['config']->get('storage.default'));
            return new Filesystem($adapter);
        });
    }
}
