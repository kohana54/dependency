<?php

namespace spec\Fuel\Dependency;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InjectorSpec extends ObjectBehavior
{
	function it_is_initializable()
	{
		$this->shouldHaveType('Fuel\Dependency\Injector');
	}

	function it_should_allow_to_make_a_class()
	{
		$this->make('stdClass')->shouldHaveType('stdClass');
	}

	function it_should_allow_to_make_a_class_with_a_dependency()
	{
		$class = 'Fuel\Dependency\Stub\SimpleClass';

		$object = $this->make($class);

		$object->shouldHaveType($class);

		$object->dependency->shouldHaveType('stdClass');
	}
}
