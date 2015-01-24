<?php

class ConstructorMixedScalar
{
	public $myScalar;
	public $dep;

	public function __construct($myScalar, DependedOn $dep)
	{
		$this->myScalar = $myScalar;
		$this->dep = $dep;
	}
}

/**
 * A Class with unknown constructor args
 *
 * Class ConstructorFuncGetArgs
 */
class ConstructorFuncGetArgs
{
	public $args;

	public function __construct(DependedOn $dep, $foo)
	{
		$this->args = func_get_args ();
	}
}