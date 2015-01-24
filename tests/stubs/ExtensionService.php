<?php

use Fuel\Dependency\ServiceProvider;
use Fuel\Dependency\Container;

class ExtensionService implements ServiceProvider
{
	public $provides = true;

	public function provide(Container $container)
	{
		$container->extension('extension', function($container, $instance)
		{
			$instance->extension = 'This Works!';
		});
	}
}
