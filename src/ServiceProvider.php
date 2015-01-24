<?php
/**
 * @package    Kohana\Dependency
 * @version    2.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2014 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Kohana\Dependency;

interface ServiceProvider
{
	/**
	 * Provides list of identifiers
	 *
	 * @var array|boolean
	 */

	public function provide(Container $container);
}
