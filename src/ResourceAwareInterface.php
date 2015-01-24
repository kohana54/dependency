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

interface ResourceAwareInterface
{
	/**
	 * Registers a resource
	 *
	 * @param string $identifier
	 * @param mixed  $resource
	 * @param boolean $singleton
	 *
	 * @return $this
	 */
	public function register($identifier, $resource, $singleton);

	/**
	 * Injects an instance
	 *
	 * @param string $identifier
	 * @param mixed  $instance
	 *
	 * @return $this
	 */
	public function bind($identifier, $instance);

	/**
	 * Resolves an instance from a resource
	 *
	 * @param string $identifier
	 * @param array  $arguments
	 *
	 * @return mixed
	 */
	public function get($identifier, array $arguments = []);

	/**
	 * Creates a new instance from a resource
	 *
	 * @param string $identifier
	 * @param array  $arguments
	 *
	 * @return mixed
	 */
	public function factory($identifier, array $arguments = []);

}
