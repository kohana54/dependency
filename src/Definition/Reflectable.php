<?php
/**
 * @package    Fuel\Dependency
 * @version    2.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2014 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Fuel\Dependency\Definition;

use Fuel\Dependency\Context;
use Fuel\Dependency\Exception;

/**
 * Common implementation of reflectable definitions
 */
abstract class Reflectable extends Base
{
	/**
	 * Loop through a set of parameters and fill up a definition with dependencies
	 *
	 * @param \ReflectionParameter[] $parameters
	 * @param self                   $definition
	 */
	protected static function reflectParameters(array $parameters, self $definition, $alias)
	{
		foreach ($parameters as $parameter)
		{
			if ( ! $dependency = $parameter->getClass())
			{
				if ($parameter->isDefaultValueAvailable())
				{
					$definition->addArgument($parameter->getDefaultValue());

					continue;
				}

				throw Exception\UnresolvableDependency::causedByNonClassParameter($parameter->getName(), $alias);
			}

			$definition->addArgument($dependency->getName());
		}
	}
}
