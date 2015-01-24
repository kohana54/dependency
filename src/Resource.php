<?php
/**
 * @package    Kohana\Dependency
 * @version    2.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2014 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Kohana\Dependency;

use ReflectionClass;
use ReflectionParameter;

class Resource
{
	const RAW_INJECTION_PREFIX = ':';

	/**
	 * @var mixed $translation
	 */
	public $translation;

	/**
	 * @var boolean $preferSingleton
	 */
	public $preferSingleton = false;

	public function __construct($translation)
	{
		$this->translation = $translation;
	}

	/**
	 * Sets the resource to prefer singleton usage
	 *
	 * @param boolean $prefer
	 *
	 * @return $this
	 */
	public function preferSingleton($prefer = true)
	{
		$this->preferSingleton = $prefer;

		return $this;
	}

	/**
	 * Resolves a constructor
	 *
	 * @param ResolveContext $context
	 * @param array          $arguments
	 *
	 * @return mixed
	 */
	public function resolve(Container $container, array $arguments = [])
	{
		if (is_callable($this->translation))
		{
			$callback = $this->translation;
			array_unshift($arguments, $container);

			// calling the method directly is faster then call_user_func_array() !
			switch (count($arguments))
			{
				case 1:
					return $callback($arguments[0]);

				case 2:
					return $callback($arguments[0], $arguments[1]);

				case 3:
					return $callback($arguments[0], $arguments[1], $arguments[2]);

				case 4:
					return $callback($arguments[0], $arguments[1], $arguments[2], $arguments[3]);

				default:
					return call_user_func_array($callback, $arguments);
			}
		}

		$class = new ReflectionClass($this->translation);

		// Raise an error when the class is not instantiatable.
		if ( ! $class->isInstantiable())
		{
			throw new ResolveException('Class '.$this->translation.' is not instantiable.');
		}

		// Return a new instance when there is no constructor
		if ( ! $constructor = $class->getConstructor())
		{
			return new $this->translation;
		}

		// Retrieve the constructor arguments
		$parameters = $constructor->getParameters();

		$inject = [];

		// Resolve the remaining parameters, removing any :prefixed arguments from $arguments
		foreach ($parameters as $parameter)
		{
			$inject[] = $this->resolveParameter($container, $parameter, $arguments);
		}

		// Append any remainging args
		$inject = array_merge($inject, $arguments);

		// return a new instance with arguments.
		return $class->newInstanceArgs($inject);
	}

	/**
	 * Resolves a constructor parameter
	 *
	 * @param ResolveContext      $context
	 * @param ReflectionParameter $parameter
	 *
	 * @return mixed
	 *
	 * @throws ResolveException  If the parameter is unresolvable
	 */
	protected function resolveParameter(Container $container, ReflectionParameter $parameter, &$arguments = [])
	{
		// raw parameter has been provided
		if(isset($arguments[static::RAW_INJECTION_PREFIX . $parameter->name]))
		{
			$raw = $arguments[static::RAW_INJECTION_PREFIX . $parameter->name];

			unset($arguments[static::RAW_INJECTION_PREFIX . $parameter->name]);

			return $raw;
		}

		// is not a class
		if ($parameter->isDefaultValueAvailable())
		{
			return $parameter->getDefaultValue();
		}

		if ($class = $parameter->getClass())
		{
			try
			{
				return $container->get($class->name);
			}
			catch (ResolveException $e)
			{
				// Let this one pass, fall back to default value
			}
		}

		if (isset($e))
		{
			throw $e;
		}

		throw new ResolveException('Could not resolve parameter '.$parameter->name.' for class '.$this->translation);
	}
}
