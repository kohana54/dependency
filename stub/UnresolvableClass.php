<?php
/**
 * @package    Fuel\Dependency
 * @version    2.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2014 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Fuel\Dependency\Stub;

class UnresolvableClass
{
	public $dependency;

	public function __construct($dependency)
	{
		$this->dependency = $dependency;
	}
}
