<?php
/**
 * @package    Fuel\Dependency
 * @version    2.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2014 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Fuel\Dependency;

class Injector
{
	public function make($className)
	{
		return $this->makeClass($className);
	}

	protected function makeClass($className)
	{
		try
		{
			$classReflector = new \ReflectionClass($className);
		}
		catch (\ReflectionException $e)
		{
			//nnot found
		}

		if ( ! $classReflector->isInstantiable())
		{
			// cannot be instantiated
		}

		if ( ! $constructorReflector = $classReflector->getConstructor())
		{
			return new $className;
		}

		$parameters = $constructorReflector->getParameters();

		$args = $this->generateArgs($parameters);

		return $classReflector->newInstanceArgs($args);
	}

	protected function generateArgs(array $parameters)
	{
		$args = [];

		foreach ($parameters as $parameterReflector)
		{
			if ($classReflector = $parameterReflector->getClass())
			{
				$args[] = $this->make($classReflector->getName());
			}
		}

		return $args;
	}
}
