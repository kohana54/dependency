<?php

namespace spec\Fuel\Dependency;

use Fuel\Dependency\Definition;
use Fuel\Dependency\Stub\SimpleClass;
use PhpSpec\ObjectBehavior;

class ContainerSpec extends ObjectBehavior
{
	function it_is_initializable()
	{
		$this->shouldHaveType('Fuel\Dependency\Container');
	}

	function it_should_allow_to_register_a_factory_closure()
	{
		$this->isRegistered('test')->shouldReturn(false);
		$this->isResolvable('test')->shouldReturn(false);

		$this->register('test', function() {
			return new \stdClass;
		})->shouldHaveType('Fuel\Dependency\Definition\Factory');

		$this->isRegistered('test')->shouldReturn(true);
		$this->isResolvable('test')->shouldReturn(true);
	}

	function it_should_allow_to_register_a_class()
	{
		$this->isRegistered('Fuel\Dependency\Stub\SimpleClass')->shouldReturn(false);
		$this->isResolvable('Fuel\Dependency\Stub\SimpleClass')->shouldReturn(true);

		$this->register('Fuel\Dependency\Stub\SimpleClass')->shouldHaveType('Fuel\Dependency\Definition\Concrete');

		$this->isRegistered('Fuel\Dependency\Stub\SimpleClass')->shouldReturn(true);
		$this->isResolvable('Fuel\Dependency\Stub\SimpleClass')->shouldReturn(true);
	}

	function it_should_throw_an_exception_when_concrete_cannot_be_registered()
	{
		$this->shouldThrow('Fuel\Dependency\Exception\DefinitionNotFound')->duringRegister('invalid', true);
	}

	function it_should_allow_to_register_a_singleton()
	{
		$this->register('test', 'Fuel\Dependency\Stub\SimpleClass', true)->shouldHaveType('Fuel\Dependency\Definition\Concrete');
		$this->isSingleton('test');
	}

	function it_should_allow_to_add_a_definition(Definition $definition)
	{
		$this->isRegistered('test')->shouldReturn(false);

		$this->addDefinition('test', $definition);

		$this->isRegistered('test')->shouldReturn(true);
	}

	function it_should_allow_to_forge_a_registered_factory_definition()
	{
		$this->register('test', function($context, \stdClass $arg) {
			return new SimpleClass($arg);
		})->addArgument('stdClass');

		$this->forge('test')->shouldHaveType('Fuel\Dependency\Stub\SimpleClass');
	}

	function it_should_allow_to_forge_a_registered_concrete_definition()
	{
		$this->register('Fuel\Dependency\Stub\SimpleClass')->addArgument('stdClass');

		$this->forge('Fuel\Dependency\Stub\SimpleClass')->shouldHaveType('Fuel\Dependency\Stub\SimpleClass');
	}

	function it_should_throw_an_exception_when_trying_to_resolve_unresolvable_dependency()
	{
		$this->shouldThrow('Fuel\Dependency\Exception\UnresolvableDependency')->duringForge(true);
	}

	function it_should_throw_an_exception_when_trying_to_resolve_recursive_dependency()
	{
		$this->shouldThrow('Fuel\Dependency\Exception\RecursiveDependency')->duringForge('Fuel\Dependency\Stub\RecursiveClass');
	}

	function it_should_allow_to_resolve_a_singleton()
	{
		$this->register('test', 'Fuel\Dependency\Stub\SimpleClass', true)->addArgument('stdClass');

		$this->isSingleton('test')->shouldReturn(true);
		$this->hasInstance('test')->shouldReturn(false);

		$instance = $this->resolve('test');
		$instance->shouldHaveType('Fuel\Dependency\Stub\SimpleClass');
		$this->hasInstance('test')->shouldReturn(true);
		$this->resolve('test')->shouldReturn($instance);
	}

	function it_should_allow_to_resolve_a_multiton()
	{
		$this->register('test', 'Fuel\Dependency\Stub\SimpleClass')->addArgument('stdClass');

		$this->hasInstance('test', 'test')->shouldReturn(false);

		$instance = $this->multiton('test', 'test');
		$instance->shouldHaveType('Fuel\Dependency\Stub\SimpleClass');
		$this->hasInstance('test', 'test')->shouldReturn(true);
		$this->multiton('test', 'test')->shouldReturn($instance);
	}

	function it_should_allow_to_fallback_normal_resolution_when_instance_is_not_passed_to_multiton()
	{
		$this->register('test', 'Fuel\Dependency\Stub\SimpleClass', true)->addArgument('stdClass');

		$this->hasInstance('test')->shouldReturn(false);

		$instance = $this->multiton('test');
		$instance->shouldHaveType('Fuel\Dependency\Stub\SimpleClass');
		$this->hasInstance('test')->shouldReturn(true);
		$this->multiton('test')->shouldReturn($instance);
	}
}
