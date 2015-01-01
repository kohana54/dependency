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

use Fuel\Dependency\Definition\Base;
use Fuel\Dependency\Context;

class ExampleDefinition extends Base
{
	/**
	 * {@inheritdoc}
	 */
	public function resolve(Context $context, array $args = [])
	{
		// noop
	}
}
