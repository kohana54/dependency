<?php

use Kohana\Dependency\ServiceProvider;
use Kohana\Dependency\Container;

class InjectingService implements ServiceProvider
{
	public $provides = array('service');

	public function provide(Container $container)
	{
		$container->bind('service', $this);
	}
}
