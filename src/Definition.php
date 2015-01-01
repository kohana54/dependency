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
 * Definition holds information about the concrete implementation
 */
interface Definition
{
	/**
	 * Resolve logic
	 *
	 * @param Context $context
	 * @param array   $args
	 *
	 * @return object
	 *
	 * @since 2.0
	 */
	public function resolve(Context $context, array $args = []);

	public function addArgument($arg);

	public function addArguments(array $args);

	public function addMethodCall($method, array $args);
}
