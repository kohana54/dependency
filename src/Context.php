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
 * Context for each resolve containing the container itself, an optional instance name and if it is being resolved as a multiton instance
 */
class Context
{
	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * Name of instance
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Checks whether this is a multiton resolve
	 *
	 * @var boolean
	 */
	protected $multiton = false;

	/**
	 * @param Container   $container
	 * @param string|null $name
	 * @param boolean     $multiton
	 */
	function __construct(Container $container, $name = null, $multiton = false)
	{
		$this->container = $container;
		$this->name = $name;
		$this->multiton = $multiton;
	}

	/**
	 * Returns the Container
	 *
	 * @return Container
	 *
	 * @since 2.0
	 */
	public function getContainer()
	{
		return $this->container;
	}

	/**
	 * Returns the name of instance
	 *
	 * @return string
	 *
	 * @since 2.0
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Checks whether the instance is a multiton
	 *
	 * @return boolean
	 *
	 * @since 2.0
	 */
	public function isMultiton()
	{
		return $this->multiton;
	}
}
