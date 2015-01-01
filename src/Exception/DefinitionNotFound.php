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
 * Thrown when a concrete cannot be registered with any type of definitions
 */
class DefinitionNotFound extends \Exception
{
	/**
	 * @param string $concrete
	 */
	public function __construct($concrete)
	{
		parent::__construct(sprintf('Unable to identify an appropriate definition for "%s"', $this->normalizeConcrete($concrete)));
	}

	protected function normalizeConcrete($concrete)
	{
		if (is_null($concrete) or is_bool($concrete)) {
			return var_export($concrete, true);
		}
		elseif (is_scalar($concrete) or (is_object($concrete) and method_exists($concrete, '__toString')))
		{
			return (string) $concrete;
		}

		return sprintf('["s"]', gettype($concrete));
	}
}
