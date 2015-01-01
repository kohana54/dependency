<?php
/**
 * @package    Fuel\Dependency
 * @version    2.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2014 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Fuel\Dependency\Exception;

/**
 * Thrown when a class is not instantiable
 */
class NonInstantiableClass extends \Exception
{
	/**
	 * @param string $className
	 */
	public function __construct($className)
	{
		parent::__construct(sprintf('Class "%s" is not instantiable', $className));
	}
}
