<?php

namespace Wlion\Helpers\Providers;

use Illuminate\Support\ServiceProvider;
use Wlion\Helpers\Form;
use Wlion\Helpers\Format;
use Wlion\Helpers\Html;
use Wlion\Helpers\Util;

class HelpersServiceProvider extends ServiceProvider {
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = FALSE;
	
	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot() {
		$this->package('wlion/helpers');
	}
	
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {
		$this->app['form'] = $this->app->share(function($app) {
			return new Form($app['html']);
		});
		$this->app['format'] = $this->app->share(function($app) {
			return new Format;
		});
		$this->app['html'] = $this->app->share(function($app) {
			return new Html($app['url']);
		});
		$this->app['util'] = $this->app->share(function($app) {
			return new Util;
		});
	}
	
	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {
		return array('form', 'format', 'html', 'util');
	}
}
