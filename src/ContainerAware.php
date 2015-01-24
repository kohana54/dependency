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

use Closure;

/**
 * Implements container aware logic
 *
 * Classes using this class should implement ResourceAwareInterface
 */
trait ContainerAware
{
	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * Sets the container
	 *
	 * @param Container $container
	 *
	 * @return $this
	 */
	public function setContainer(Container $container)
	{
		$this->container = $container;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function register($identifier, $resource, $singleton = TRUE)
	{
		$this->container->register($identifier, $resource, $singleton);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get($identifier, array $arguments = [])
	{
		return $this->container->get($identifier, $arguments);
	}

	/**
	 * {@inheritdoc}
	 */
	public function bind($identifier, $instance)
	{
		$this->container->register($identifier, $instance, TRUE);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function factory($identifier, array $arguments = [])
	{
		return $this->container->factory($identifier, $arguments);
	}

	/**
	 * Attaches extensions to an identifier
	 *
	 * @param string         $identifier
	 * @param string|Closure $extension  the generic extension, or a closure implementing the extension
	 *
	 * @return $this
	 */
	public function extend($identifier, $extension)
	{
		$this->container->extend($identifier, $extension);

		return $this;
	}

	/**
	 * Defines a generic resource extension
	 *
	 * @param string  $identifier
	 * @param Closure $extension
	 *
	 * @return $this
	 */
	public function extension($identifier, Closure $extension)
	{
		$this->container->extension($identifier, $extension);

		return $this;
	}
}
