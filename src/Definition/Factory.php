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
 * Factory callable definition
 */
class Factory extends Reflectable
{
	/**
	 * Factory callable
	 *
	 * @var callable
	 */
	protected $callable;

	/**
	 * @param callable $callable
	 */
	public function __construct(callable $callable)
	{
		$this->callable = $callable;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function resolveObject(Context $context, array $args)
	{
		array_unshift($args, $context);

		return call_user_func_array($this->callable, $args);
	}

	/**
	 * Reflect a callable and return a definition filled by dependencies
	 *
	 * @param callable $callable
	 *
	 * @return self
	 */
	public static function reflect(callable $callable)
	{
		$function = new \ReflectionFunction($callable);

		$definition = new self($callable);

		$parameters = $function->getParameters();

		array_shift($parameters);

		self::reflectParameters($parameters, $definition, '[callable]');

		return $definition;
	}
}
