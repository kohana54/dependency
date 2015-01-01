<?php

namespace spec\Fuel\Dependency\Definition;

use Fuel\Dependency\Context;
use Fuel\Dependency\Container;
use PhpSpec\ObjectBehavior;

class ConcreteSpec extends ObjectBehavior
{
	function let()
	{
		$this->beConstructedWith('stdClass');
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Fuel\Dependency\Definition\Concrete');
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

		$this->beConstructedWith('Fuel\Dependency\Stub\SimpleClass');
		$this->addArgument('stdClass');

		$this->resolve($context)->shouldHaveType('Fuel\Dependency\Stub\SimpleClass');
	}

	function it_should_allow_to_resolve_with_method_calls(Context $context, Container $container)
	{
		$container->resolve('stdClass')->willReturn(new \stdClass);
		$container->isResolvable('stdClass')->willReturn(true);
		$context->getContainer()->willReturn($container);

		$this->beConstructedWith('Fuel\Dependency\Stub\ClassWithSetter');
		$this->addMethodCall('setDependency', ['stdClass']);

		$this->resolve($context)->shouldHaveType('Fuel\Dependency\Stub\ClassWithSetter');
	}

	function it_should_allow_to_reflect_a_class(Context $context, Container $container)
	{
		$container->resolve('stdClass')->willReturn(new \stdClass);
		$container->isResolvable('stdClass')->willReturn(true);
		$context->getContainer()->willReturn($container);

		$definition = $this->reflect('Fuel\Dependency\Stub\SimpleClass');
		$definition->shouldHaveType('Fuel\Dependency\Definition\Concrete');

		$definition->resolve($context)->shouldHaveType('Fuel\Dependency\Stub\SimpleClass');
	}

	function it_should_allow_to_reflect_a_class_without_constructor(Context $context)
	{
		$definition = $this->reflect('Fuel\Dependency\Stub\ClassWithSetter');
		$definition->shouldHaveType('Fuel\Dependency\Definition\Concrete');

		$definition->resolve($context)->shouldHaveType('Fuel\Dependency\Stub\ClassWithSetter');
	}

	function it_should_throw_an_exception_when_class_is_not_instantiable()
	{
		$this->shouldThrow('Fuel\Dependency\Exception\NonInstantiableClass')->duringReflect('Fuel\Dependency\Stub\NonInstantiableClass');
	}

	function it_should_throw_an_exception_when_class_parameter_is_unresolvable()
	{
		$this->shouldThrow('Fuel\Dependency\Exception\UnresolvableDependency')->duringReflect('Fuel\Dependency\Stub\UnresolvableClass');
	}
}
