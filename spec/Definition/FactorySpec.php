<?php

namespace spec\Fuel\Dependency\Definition;

use Fuel\Dependency\Context;
use Fuel\Dependency\Container;
use Fuel\Dependency\Stub\SimpleClass;
use Fuel\Dependency\Stub\ClassWithSetter;
use PhpSpec\ObjectBehavior;

class FactorySpec extends ObjectBehavior
{
	function let()
	{
		$this->beConstructedWith(function($context) {
			return new \stdClass;
		});
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Fuel\Dependency\Definition\Factory');
		$this->shouldHaveType('Fuel\Dependency\Definition');
	}

	function it_should_allow_to_resolve_without_args(Context $context)
	{
		$this->resolve($context)->shouldHaveType('stdClass');
	}

	function it_should_allow_to_resolve_with_args(Context $context, Container $container)
	{
		$container->resolve('stdClass')->willReturn(new \stdClass);
		$container->isResolvable('stdClass')->willReturn(true);
		$context->getContainer()->willReturn($container);

		$this->beConstructedWith(function($context, \stdClass $arg) {
			return new SimpleClass($arg);
		});
		$this->addArgument('stdClass');

		$this->resolve($context)->shouldHaveType('Fuel\Dependency\Stub\SimpleClass');
	}

	function it_should_allow_to_resolve_with_method_calls(Context $context, Container $container)
	{
		$container->resolve('stdClass')->willReturn(new \stdClass);
		$container->isResolvable('stdClass')->willReturn(true);
		$context->getContainer()->willReturn($container);

		$this->beConstructedWith(function($context) {
			return new ClassWithSetter;
		});
		$this->addMethodCall('setDependency', ['stdClass']);

		$this->resolve($context)->shouldHaveType('Fuel\Dependency\Stub\ClassWithSetter');
	}

	function it_should_allow_to_reflect_a_callable(Context $context, Container $container)
	{
		$container->resolve('stdClass')->willReturn(new \stdClass);
		$container->isResolvable('stdClass')->willReturn(true);
		$context->getContainer()->willReturn($container);

		$definition = $this->reflect(function(Context $context, \stdClass $arg) {
			return new SimpleClass($arg);
		});
		$definition->shouldHaveType('Fuel\Dependency\Definition\Factory');

		$definition->resolve($context)->shouldHaveType('Fuel\Dependency\Stub\SimpleClass');
	}

	function it_should_throw_an_exception_when_callable_parameter_is_unresolvable()
	{
		$this->shouldThrow('Fuel\Dependency\Exception\UnresolvableDependency')->duringReflect(function($context, $anotherParam) {});
	}
}
