<?php

use Kohana\Dependency\ServiceProvider;
use Kohana\Dependency\Container;

class RegisteringService implements ServiceProvider
{
	public $provides = TRUE;

	public function provide(Container $container)
	{
		$container->singleton('factory', function($container) {
			return (object) compact('container', 'arguments');
		});

		$container->extend('factory', function($container, $instance)
		{
			$instance->extension = 'This Works!';
		});

		$factory = $container->factory('factory');

		$container->singleton('resolve', function($container) {
			return (object) compact('container', 'arguments');
		});

		$resolve = $container->get('resolve');

		$container->singleton('resolveSingleton', function($container) {
			return (object) compact('container', 'arguments');
		});

		$container->bind('from.service', function($container) use ($factory, $resolve) {
			return (object) compact('factory', 'resolve');
		});
	}
}
