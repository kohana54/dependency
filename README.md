# Kohana54 Dependency

[![Build Status](https://travis-ci.org/kohana54/dependency.svg?branch=master)](https://travis-ci.org/kohana54/dependency)
[![Code Coverage](https://scrutinizer-ci.com/g/kohana54/dependency/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/kohana54/dependency/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kohana54/dependency/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/kohana54/dependency/?branch=master)
[![HHVM Status](http://hhvm.h4cc.de/badge/kohana54/dependency.svg)](http://hhvm.h4cc.de/package/kohana54/dependency)

This is the Kohana54 Dependency package, a fork of Fuel/Dependency

The primary differences between Kohana54/Dependency and Fuel/Dependency are:

* Support of container-interop/container-interop ~1.0 interface
* Removal of multiton pattern
* Default behaviour is to share/bind instances rather than create factories
* Rename methods to be more Kohana-esq
* Supports injection of non Object constructor args

## Contents

The Dependency package is a dependency injection implementation for the Kohana54 framework. In order to provide this functionality the package is responsible for:

* Registering dependencies
* Resolving dependencies
* Storing instances for singletons and multitons.
* Building instances with resolved constructor arguments.

The package resolves dependencies through injected instances, resources and by contacting Service Providers.

## The Container

The container is the primary component of the dependency package and ties all the parts together. You can look at this as the (PHP) object store. The container is where you register resources, service providers and retrieve dependencies.

Create a new `Container`

``` php
$container = new Kohana54\Dependency\Container;
```

## Resources

A resource is either a class string name or a closure which returns an instance or a class name.

#### String resource:

``` php
// Register
$container->register('string', 'stdClass');

// Resolve
$instance = $container->resolve('string');
```

#### Closure resource:

``` php

// Register shared
$container->register('closure.object', function() {
	return new stdClass;
});

// Resolve
$instance = $container->resolve('closure.object');

$instance2 = $container->resolve('closure.object');

$instance1 === $instance2
// TRUE

```

## Extending

Using extensions is a great way to inject additional resources into instances:

``` php
$container->register('extendable', 'stdClass');

$container->extend('extendable', function($container, $instance)
{
	$instance->extended = true;
});

$instance = $container->resolve('extendable');
$instance->extended;
// > true
```

If you have extensions you want to apply to multiple resources, you can also define generic extensions:

``` php
$container->register('extendable', 'stdClass');

$container->extension('isExtended', function($container, $instance)
{
	$instance->extended = true;
});

$container->extend('extendable', 'isExtended');

$instance = $container->resolve('extendable');
$instance->extended;
// > true
```

## Automatic Injection (Inversion of Control)

Inversion of Control (IOC) allows classes registered with the DIC to have dependencies automatically injected when
resolved. This allows behaviours to be injected without needing to create hard dependencies between classes.

At this moment the current implementation is slightly flawed in that the class that that is being injected has to be
an actual class, meaning that other classes cannot be aliased and injected.

```php
class Hello
{
	public function speak()
	{
		echo "Hello world\n";
	}
}

class Main
{
	protected $hello;

	public function __construct(Hello $fake)
	{
		$this->hello = $fake;
	}

	public function talk()
	{
		$this->hello->speak();
	}
}

$container = new Fuel\Dependency\Container;
$container->register('Hello', 'Hello');
$container->register('Main', 'Main');
$container->resolve('Main')->talk();
```

## Non Object Constructor Injection

```php

class Hello {}

class Main
{
	protected $args;

	public function __construct(Hello $fake, $arg1, $arg2, $arg3 )
	{
		$this->args = func_get_args ();
	}

	public function debug()
	{
		var_dump($this->args);
	}
}

$container = new Fuel\Dependency\Container;
$container->register('Hello', 'Hello');
$container->register('Main', 'Main');
$container->resolve('Main', ['oranges', 'apples', 'pears', ':arg1' => 'apples', 'bananas'])->debug();

array(1) {
  [0] =>
  class stdClass#1 (0) {
  }
}
```

## Service Providers

Service providers are used to expose packages to the Container. A Service
Provider can provide the container with resources but also act on a namespace.
A namespace is a string prefix which maps identifiers to the providers factory method.

``` php
use Fuel\Dependency\ServiceProvider;

class MyProvider extends ServiceProvider
{
	public $provides = ['some.identifier', 'other.resource'];

	public function forge($suffix, array $arguments = [])
	{
		$instance = new Something($suffix);
		$instance->configure($arguments);
		$instance->connection = $this->resolve('database.connection');

		return $instance;
	}

	public function provide(Container $container)
	{
		$container->register('some.identifier', 'stdClass');
		$container->registerSingleton('other.resource', function($container) {
			return new Something($container->resolve('database.connection'));
		));
	}
}
```
