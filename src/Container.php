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

class Container
{
	/**
	 * @var Definition[]
	 */
	protected $definitions = [];

	/**
	 * Caches the currently processed alias
	 *
	 * @var array
	 */
	private $buildStack = [];

	/**
	 * Registers a new definition
	 *
	 * @param string $alias
	 * @param mixed  $concrete
	 *
	 * @return Definition
	 *
	 * @since 2.0
	 */
	public function register($alias, $concrete = null)
	{
		if (is_null($concrete)) {
			$concrete = $alias;
		}

		if (is_callable($concrete))
		{
			$definition = new Definition\Factory($concrete);
		}
		elseif (is_string($concrete) and class_exists($concrete))
		{
			$definition = new Definition\Concrete($concrete);
		}
		else
		{
			throw new Exception\DefinitionNotFound($concrete);
		}

		return $this->definitions[$alias] = $definition;
	}

	/**
	 * Checks whether an alias has been registered as a definition
	 *
	 * @param string  $alias
	 *
	 * @return boolean
	 *
	 * @since 2.0
	 */
	public function isRegistered($alias)
	{
		return isset($this->definitions[$alias]);
	}

	/**
	 * Checks whether an alias is resolvable by the container
	 *
	 * @param string  $alias
	 *
	 * @return boolean
	 *
	 * @since 2.0
	 */
	public function isResolvable($alias)
	{
		return $this->isRegistered($alias) or class_exists($alias);
	}

	/**
	 * Adds a definition to the container
	 *
	 * @param string     $alias
	 * @param Definition $definition
	 *
	 * @since 2.0
	 */
	public function addDefinition($alias, Definition $definition)
	{
		$this->definitions[$alias] = $definition;
	}

	public function resolve($alias, array $args = [])
	{
		return $this->forge($alias, $args);
	}

	public function multiton($alias, $instance, array $args = [])
	{
		$context = new Context($this, $instance, true);
	}

	/**
	 * Creates a new instance
	 *
	 * @param string $alias
	 * @param array  $args
	 *
	 * @return object
	 */
	public function forge($alias, array $args = [])
	{
		$this->preventRecursiveDependency($alias);

		if ($this->isRegistered($alias))
		{
			$definition = $this->definitions[$alias];
		}
		elseif (class_exists($alias))
		{
			$definition = Definition\Concrete::reflect($alias);
			$this->addDefinition($alias, $definition);
		}

		if (!isset($definition))
		{
			throw Exception\UnresolvableDependency::causedByBeingNotFound($alias);
		}

		$context = new Context($this);

		$object = $definition->resolve($context, $args);

		$this->clearRecursiveDependency($alias);

		return $object;
	}

	/**
	 * Prevents the Container from getting into an endless loop
	 * by recursively resolving the same alias
	 *
	 * @param string $alias
	 *
	 * @throws Exception\RecursiveDependency If $alias is already being resolved
	 */
	protected function preventRecursiveDependency($alias)
	{
		if (isset($this->buildStack[$alias]))
		{
			throw new Exception\RecursiveDependency($alias);
		}

		$this->buildStack[$alias] = true;
	}

	/**
	 * Removes an alias from the list of build stack
	 *
	 * @param string $alias
	 */
	protected function clearRecursiveDependency($alias)
	{
		unset($this->buildStack[$alias]);
	}
}
