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
 * Thrown when an alias or one of its dependencies depend on itself
 */
class RecursiveDependency extends \Exception
{
	/**
	 * @param string $alias
	 */
	public function __construct($alias)
	{
		parent::__construct(sprintf('"%s" is already being resolved', $alias));
	}
}
