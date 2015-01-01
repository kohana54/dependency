<?php
/**
 * @package    Fuel\Dependency
 * @version    2.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2014 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Fuel\Dependency\Definition;

use Fuel\Dependency\Context;
use Fuel\Dependency\Exception;

/**
 * Concrete implementation definition
 */
class Concrete extends Base
{
	/**
	 * @var string
	 */
	protected $className;

	/**
	 * @param string $className
	 */
	public function __construct($className)
	{
		$this->className = $className;
	}

	/**
	 * {@inheritdoc}
	 */
	public function resolve(Context $context, array $args = [])
	{
		$args = $this->resolveArguments($context, array_merge($this->arguments, $args));
		$class = new \ReflectionClass($this->className);

		$object = $class->newInstanceArgs($args);

		$this->invokeMethods($context, $object);

		return $object;
	}

	/**
	 * Reflect a class and return a definition filled by dependencies
	 *
	 * @param string $className
	 *
	 * @return self
	 */
	public static function reflect($className)
	{
		$class = new \ReflectionClass($className);

		if ( ! $class->isInstantiable())
		{
			throw new Exception\NonInstantiableClass($className);
		}

		$definition = new self($className);

		if ( ! $constructor = $class->getConstructor())
		{
			return $definition;
		}

		foreach ($constructor->getParameters() as $parameter)
		{
			if ( ! $dependency = $parameter->getClass())
			{
				if ($parameter->isDefaultValueAvailable())
				{
					$definition->addArgument($parameter->getDefaultValue());

					continue;
				}

				throw Exception\UnresolvableDependency::causedByNonClassParameter($parameter->getName(), $className);
			}

			$definition->addArgument($dependency->getName());
		}

		return $definition;
	}
}