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

/**
 * Implements pure injection logic without any container
 */
class Injector
{
	/**
	 * Caches the currently processed class
	 *
	 * @var array
	 */
	private $beingMade = [];


	/**
	 * Creates a new instance
	 *
	 * @param string $className
	 * @param array  $definition
	 *
	 * @return object
	 */
	public function make($className, array $definition = [])
	{
		$this->preventRecursiveDependency($className);

		$object = $this->makeClass($className, $definition);

		$this->clearRecursiveDependency($className);

		return $object;
	}

	/**
	 * Creates a new class
	 *
	 * @param string $className
	 * @param array  $definition
	 *
	 * @return object
	 */
	protected function makeClass($className, array $definition = [])
	{
		// here comes the container part later
		return $this->createInstance($className, $definition);
	}

	protected function createInstance($className, array $definition = [])
	{
		try
		{
			$classReflector = new \ReflectionClass($className);
		}
		catch (\ReflectionException $e)
		{
			// not found
		}

		if ( ! $classReflector->isInstantiable())
		{
			// cannot be instantiated
			// interfaces and abstracts should be handled here
		}

		if ( ! $constructorReflector = $classReflector->getConstructor())
		{
			return new $className;
		}

		$args = $this->generateArgs($constructorReflector);

		return $classReflector->newInstanceArgs($args);
	}

	protected function generateArgs(\ReflectionFunctionAbstract $function, array $definition = [])
	{
		$parameters = $function->getParameters();

		$args = [];

		foreach ($parameters as $parameterReflector)
		{
			if ( ! $classReflector = $parameterReflector->getClass())
			{
				$args[] = null;
			}
			elseif ($parameterReflector->isDefaultValueAvailable())
			{
				$args[] = $parameterReflector->getDefaultValue();
			}
			elseif ($parameterReflector->isOptional())
			{
				$args[] = null;
			}
			else
			{
				$args[] = $this->make($classReflector->getName());
			}
		}

		return $args;
	}

	/**
	 * Prevents the Injector from getting into an endless loop
	 *
	 * @param string $className
	 *
	 * @throws RecursiveDependencxException If $className is already being made
	 */
	private function preventRecursiveDependency($className)
	{
		if (isset($this->beingMade[$className]))
		{
			throw new RecursiveDependencyException;
		}

		$this->beingMade[$className] = true;
	}

	/**
	 * Removes a class from the list of actually being made classes
	 *
	 * @param string $className
	 */
	private function clearRecursiveDependency($className)
	{
		unset($this->beingMade[$className]);
	}
}
