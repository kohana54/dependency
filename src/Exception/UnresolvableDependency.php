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
 * Thrown when a dependency cannot be resolved
 */
class UnresolvableDependency extends \Exception
{
	public static function causedByBeingNotFound($alias)
	{
		return new self(sprintf('Unable to resolve dependency "%s"', $alias));
	}

	public static function causedByNonClassParameter($parameterName, $className)
	{
		return new self(sprintf('Unable to resolve a non-class dependency of "%s" for "%s"', $parameterName, $className));
	}
}
