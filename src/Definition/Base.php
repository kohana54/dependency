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

use Fuel\Dependency\Definition;
use Fuel\Dependency\Context;

/**
 * Abstract definition implementing common logic
 */
abstract class Base implements Definition
{
	/**
	 * @var array
	 */
	protected $arguments = [];

	/**
	 * @var array
	 */
	protected $methodCalls = [];

	/**
	 * {@inheritdoc}
	 */
	public function addArgument($arg)
	{
		$this->arguments[] = $arg;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function addArguments(array $args)
	{
		foreach ($args as $arg) {
			$this->addArgument($arg);
		}

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function addMethodCall($method, array $args)
	{
		$this->methodCalls[$method] = $args;

		return $this;
	}

	/**
	 * Resolve arguments passed to the definition
	 *
	 * @param Context $context
	 * @param array   $args
	 *
	 * @return array
	 *
	 * @since 2.0
	 */
	protected function resolveArguments(Context $context, array $args)
	{
		$resolvedArguments = [];
		$container = $context->getContainer();

		foreach ($args as $arg)
		{
			if (is_string($arg) and $container->isResolvable($arg))
			{
				$arg = $container->resolve($arg);
			}

			$resolvedArguments[] = $arg;
		}

		return $resolvedArguments;
	}

	/**
	 * Invoke method calls on a resolved instance
	 *
	 * @param context $context
	 * @param mixed   $object
	 *
	 * @since 2.0
	 */
	protected function invokeMethods(Context $context, $object)
	{
		foreach ($this->methodCalls as $method => $args)
		{
			$args = $this->resolveArguments($context, $args);
			call_user_func_array([$object, $method], $args);
		}
	}
}
