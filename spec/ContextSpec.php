<?php

namespace spec\Fuel\Dependency;

use Fuel\Dependency\Container;
use PhpSpec\ObjectBehavior;

class ContextSpec extends ObjectBehavior
{
	function let(Container $container)
	{
		$this->beConstructedWith($container);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Fuel\Dependency\Context');
	}

	function it_should_have_a_container_instance(Container $container)
	{
		$this->getContainer()->shouldReturn($container);
	}

	function it_should_have_no_name_by_default()
	{
		$this->getName()->shouldReturn(null);
	}

	function it_should_allow_to_have_a_name(Container $container)
	{
		$this->beConstructedWith($container, 'name');

		$this->getName()->shouldReturn('name');
	}

	function it_should_not_be_multiton_by_default()
	{
		$this->isMultiton()->shouldReturn(false);
	}

	function it_should_allow_to_be_multiton(Container $container)
	{
		$this->beConstructedWith($container, null, true);

		$this->isMultiton()->shouldReturn(true);
	}
}
