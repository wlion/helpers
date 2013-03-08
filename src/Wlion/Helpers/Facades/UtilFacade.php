<?php

namespace Wlion\Helpers\Facades;

use Illuminate\Support\Facades\Facade;

class UtilFacade extends Facade {
	/**
	* Get the registered name of the component.
	*
	* @return string
	*/
	protected static function getFacadeAccessor() { return 'util'; }
}
