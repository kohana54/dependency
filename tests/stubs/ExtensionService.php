<?php

use Kohana\Dependency\ServiceProvider;
use Kohana\Dependency\Container;

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
