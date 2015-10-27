<?php namespace Superbalist\Shared\Libs\Storage;

use Config;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Adapter\Local;

class StorageServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 */
	public function register()
	{
		$this->app->bind('storage', function() {
			$adapter = StorageAdapterFactory::makeCached(Config::get('storage.default'));
			return new Filesystem($adapter);
		});
	}
}
