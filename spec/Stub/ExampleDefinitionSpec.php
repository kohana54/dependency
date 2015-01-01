<?php

namespace spec\Fuel\Dependency\Stub;

use PhpSpec\ObjectBehavior;

class ExampleDefinitionSpec extends ObjectBehavior
{
	function it_is_initializable()
	{
		$this->shouldHaveType('Fuel\Dependency\Stub\ExampleDefinition');
		$this->shouldHaveType('Fuel\Dependency\Definition\Base');
	}

	function it_should_allow_to_add_an_argument()
	{
		$this->addArgument('test')->shouldReturn($this);
	}

	function it_should_allow_to_add_arguments()
	{
		$this->addArguments(['test', 'test'])->shouldReturn($this);
	}

	function it_should_allow_to_add_method_calls()
	{
		$this->addMethodCall('test', [])->shouldReturn($this);
	}
}
