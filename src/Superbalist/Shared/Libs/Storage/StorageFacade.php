<?php namespace Superbalist\Shared\Libs\Storage;

use Illuminate\Support\Facades\Facade;

class StorageFacade extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'storage';
	}
}
