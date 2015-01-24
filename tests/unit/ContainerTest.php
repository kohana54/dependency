<?php
/**
 * @package    Kohana\Dependency
 * @version    2.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2014 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Kohana\Dependency;

use Codeception\TestCase\Test;

class ContainerTest extends Test
{
	/**
	 * @expectedException \Kohana\Dependency\ResolveException
	 */
	public function testResolveFail()
	{
		$container = new Container();
		$container->get('unknown.dependency');
	}

	/**
	 * @expectedException \Kohana\Dependency\ResolveException
	 */
	public function testForgeFail()
	{
		$container = new Container();
		$container->factory('unknown.dependency');
	}

	/**
	 * @expectedException \Kohana\Dependency\ResolveException
	 */
	public function testAbstractFail()
	{
		$container = new Container();
		$container->factory('AbstractClass');
	}


	public function testRegisteringService()
	{
		$container = new Container();
		$container->registerService(new \RegisteringService());
		$this->assertInstanceOf('stdClass', $container['from.service']);
		$this->assertEquals('This Works!', $container['from.service']->factory->extension);
	}

	public function testExtensionService()
	{
		$container = new Container();
		$container->registerService(new \ExtensionService());
		$container->register('id', 'stdClass');
		$container->extend('id', 'extension');
		$instance = $container['id'];
		$this->assertEquals('This Works!', $instance->extension);
	}

	public function testInjectingService()
	{
		$container = new Container();
		$container->registerServices([new \InjectingService()]);
		$this->assertInstanceOf('Kohana\Dependency\ServiceProvider', $container['service']);
	}

	public function testSingletons()
	{
		$container = new Container;
		$container->register('single', 'stdClass');
		$container->register('other', 'stdClass', FALSE);
		$this->assertTrue($container['single'] === $container['single']);
		$this->assertTrue($container['single'] !== $container['other']);
		$this->assertTrue($container['single'] !== $container->factory('single'));
		$this->assertTrue($container['single'] == $container['other']);
		$this->assertTrue($container->isInstance('single'));
	}

	public function testClassIdentifier()
	{
		$container = new Container;
		$this->assertInstanceOf('stdClass', $container['stdClass']);
	}

	public function testOffsetExists()
	{
		$container = new Container;
		$this->assertFalse(isset($container['offset']));
		$this->assertTrue(isset($container['stdClass']));
		$container['offset'] = new \stdClass;
		$this->assertTrue(isset($container['offset']));
		$container->bind('stuff', 'stuff');
		$this->assertTrue(isset($container['stuff']));
		unset($container['stuff']);
		$this->assertFalse(isset($container['stuff']));
	}

	public function testExtends()
	{
		$container = new Container;
		$container->register('id', 'stdClass');

		$container->extend('id', function($container, $instance) {
			$instance->name = 'Frank';
		});

		$container->extend('id', function($container, $instance) {
			$instance->surname = 'de Jonge';

			return $instance;
		});

		$instance = $container['id'];

		$this->assertEquals('Frank', $instance->name);
		$this->assertEquals('de Jonge', $instance->surname);
	}


	public function testExtensions()
	{
		$container = new Container;
		$container->register('id1', 'stdClass');
		$container->register('id2', 'stdClass');

		$container->extension('addName', function($container, $instance) {
			$instance->name = 'Frank';
			$instance->surname = 'de Jonge';
		});

		$container->extend('id1', 'addName');
		$container->extend('id2', 'addName');

		$container->extension('addSurname', function($container, $instance) {
			$instance->surname = 'de Oude';

			return $instance;
		});

		$container->extend('id1', 'addSurname');

		$instance = $container['id1'];
		$this->assertEquals('Frank', $instance->name);
		$this->assertEquals('de Oude', $instance->surname);

		$instance = $container['id2'];
		$this->assertEquals('Frank', $instance->name);
		$this->assertEquals('de Jonge', $instance->surname);
	}

	/**
	 * @expectedException \Kohana\Dependency\InvalidExtensionException
	 */
	public function testExtendsFailure()
	{
		$container = new Container;
		$container->register('id', 'stdClass');

		$container->extend('id', 'this_is_not_a_callable');

		$container['id'];
	}
}
