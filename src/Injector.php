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
	private $beingMade = [];

	public function make($className, array $definition = [])
	{
		$this->preventRecursiveDependency($className);

		$object = $this->makeClass($className, $definition);

		$this->unpreventRecursiveDependency($className);

		return $object;
	}

	protected function makeClass($className, array $definition = [])
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

	private function preventRecursiveDependency($className)
	{
		if (isset($this->beingMade[$className]))
		{
			throw new RecursiveDependencyException;
		}

		$this->beingMade[$className] = true;
	}

	private function unpreventRecursiveDependency($className)
	{
		unset($this->beingMade[$className]);
	}
}
