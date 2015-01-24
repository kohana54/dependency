<?php

use Fuel\Dependency\ServiceProvider;
use Fuel\Dependency\Container;

class RegisteringService implements ServiceProvider
{
	public $provides = TRUE;

	public function provide(Container $container)
	{
		$container->register('factory', function($container) {
			return (object) compact('container', 'arguments');
		});

		$container->extend('factory', function($container, $instance)
		{
			$instance->extension = 'This Works!';
		});

		$factory = $container->factory('factory');

		$container->register('resolve', function($container) {
			return (object) compact('container', 'arguments');
		});

		$resolve = $container->get('resolve');

		$container->register('resolveSingleton', function($container) {
			return (object) compact('container', 'arguments');
		});

		$container->register('from.service', function($container) use ($factory, $resolve) {
			return (object) compact('factory', 'resolve');
		}, FALSE);
	}
}
