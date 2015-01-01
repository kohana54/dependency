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
class Concrete extends Reflectable
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
	protected function resolveObject(Context $context, array $args)
	{
		$class = new \ReflectionClass($this->className);

		return $class->newInstanceArgs($args);
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

		self::reflectParameters($constructor->getParameters(), $definition, $className);

		return $definition;
	}
}
