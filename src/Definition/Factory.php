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
class Factory extends Base
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
	public function resolve(Context $context, array $args = [])
	{
		$args = $this->resolveArguments($context, array_replace($this->arguments, $args));
		array_unshift($args, $context);

		$object = call_user_func_array($this->callable, $args);

		$this->invokeMethods($context, $object);

		return $object;
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

		foreach ($function->getParameters() as $parameter)
		{
			if ( ! $dependency = $parameter->getClass())
			{
				if ($parameter->isDefaultValueAvailable())
				{
					$definition->addArgument($parameter->getDefaultValue());

					continue;
				}

				throw Exception\UnresolvableDependency::causedByNonClassParameter($parameter->getName(), '[callable]');
			}

			if ($dependency->getName() == 'Fuel\Dependency\Context')
			{
				continue;
			}

			$definition->addArgument($dependency->getName());
		}

		return $definition;
	}
}
