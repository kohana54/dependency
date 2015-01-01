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
	 * @var array
	 */
	protected $instances = [];

	/**
	 * List of resources that should be saved when resolved
	 *
	 * @var array
	 */
	protected $singletons = [];

	/**
	 * Caches the currently processed alias
	 *
	 * @var array
	 */
	protected $buildStack = [];

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
	public function register($alias, $concrete = null, $singleton = false)
	{
		if (is_null($concrete))
		{
			$concrete = $alias;
		}

		if ($singleton)
		{
			$this->singletons[] = $alias;
		}

		return $this->definitions[$alias] = $this->createDefinition($concrete);
	}

	/**
	 * Creates a new definition based on a concrete
	 *
	 * @param mixed $concrete
	 *
	 * @return Definition
	 *
	 * @since 2.0
	 */
	protected function createDefinition($concrete)
	{
		if (is_callable($concrete))
		{
			return new Definition\Factory($concrete);
		}
		elseif (is_string($concrete) and class_exists($concrete))
		{
			return new Definition\Concrete($concrete);
		}

		throw new Exception\DefinitionNotFound($concrete);
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

	/**
	 * Returns a definition based on an alias or class name
	 *
	 * @param string $alias
	 *
	 * @return Definition
	 *
	 * @throws Exception\UnresolvableDependency If definition is not registered or class not found
	 *
	 * @since 2.0
	 */
	protected function getDefinition($alias)
	{
		if ($this->isRegistered($alias))
		{
			return $this->definitions[$alias];
		}
		elseif (class_exists($alias))
		{
			$definition = Definition\Concrete::reflect($alias);
			$this->addDefinition($alias, $definition);

			return $definition;
		}

		throw Exception\UnresolvableDependency::causedByBeingNotFound($alias);
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
	 * Checks whether an alias should be resolved as a singleton
	 *
	 * @param string  $alias
	 *
	 * @return boolean
	 *
	 * @since 2.0
	 */
	public function isSingleton($alias)
	{
		return in_array($alias, $this->singletons);
	}

	/**
	 * Checks whether the container has an instance shared or not
	 *
	 * @param string $alias
	 * @param string $instance
	 *
	 * @return boolean
	 *
	 * @since 2.0
	 */
	public function hasInstance($alias, $instance = null)
	{
		if (isset($instance))
		{
			$alias .= '::'.$instance;
		}

		return isset($this->instances[$alias]);
	}

	/**
	 * Creates or returns an instance
	 *
	 * @param string $alias
	 * @param array  $args
	 *
	 * @return object
	 *
	 * @since 2.0
	 */
	public function resolve($alias, array $args = [])
	{
		if ($this->hasInstance($alias))
		{
			return $this->instances[$alias];
		}

		$object = $this->forge($alias, $args);

		if ($this->isSingleton($alias))
		{
			$this->instances[$alias] = $object;
		}

		return $object;
	}

	/**
	 * Creates or returns a multiton instance
	 *
	 * @param string $alias
	 * @param string $instance
	 * @param array  $args
	 *
	 * @return object
	 *
	 * @since 2.0
	 */
	public function multiton($alias, $instance = null, array $args = [])
	{
		if (is_null($instance)) {
			return $this->resolve($alias, $args);
		}

		$instanceName = $alias.'::'.$instance;

		if ($this->hasInstance($alias, $instance))
		{
			return $this->instances[$instanceName];
		}

		$context = new Context($this, $instance, true);

		return $this->instances[$instanceName] = $this->forge($alias, $args);
	}

	/**
	 * Creates a new instance
	 *
	 * @param string $alias
	 * @param array  $args
	 *
	 * @return object
	 *
	 * @since 2.0
	 */
	public function forge($alias, array $args = [])
	{
		$context = new Context($this);

		return $this->forgeWithContext($context, $alias, $args);
	}

	/**
	 * Creates a new instance
	 *
	 * @param Context $context
	 * @param string  $alias
	 * @param array   $args
	 *
	 * @return object
	 *
	 * @since 2.0
	 */
	protected function forgeWithContext(Context $context, $alias, array $args)
	{
		$this->preventRecursiveDependency($alias);

		$definition = $this->getDefinition($alias);

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
	 *
	 * @since 2.0
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
	 *
	 * @since 2.0
	 */
	protected function clearRecursiveDependency($alias)
	{
		unset($this->buildStack[$alias]);
	}
}
