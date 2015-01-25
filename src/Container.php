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

use ArrayAccess;
use Closure;

class Container implements ArrayAccess, ResourceAwareInterface
{
	/**
	 * @var array $resources
	 */
	protected $resources = [];

	/**
	 * @var array $instances
	 */
	protected $instances = [];

	/**
	 * @var ServiceProvider[] $services
	 */
	protected $services = [];

	/**
	 * Resource specific extensions
	 *
	 * @var array $extends
	 */
	protected $extends = [];

	/**
	 * Resource generic and reusable extensions
	 *
	 * @var array $extensions
	 */
	protected $extensions = [];

	/**
	 * {@inheritdoc}
	 */
	public function bind($identifier, $resource, $singleton = FALSE)
	{
		if ( ! $resource instanceof Resource)
		{
			$resource = new Resource($resource);
		}

		$this->resources[$identifier] = $resource;

		if ($singleton === TRUE)
		{
			$this->resources[$identifier]->preferSingleton(true);
		}

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function singleton($identifier, $resource)
	{
		return $this->bind($identifier, $resource, TRUE);
	}

	/**
	 * Registers a service provider
	 *
	 * @param ServiceProvider $service
	 *
	 * @return $this
	 */
	public function registerService(ServiceProvider $service)
	{

		// The provider does not contain a list of resources...
		if (!isset($service->provides) or $service->provides == TRUE)
		{
			// ...so we fetch them all here...
			$service->provide($this);

			// ...and prevent it from re-fetching in the future
			$service->provides = FALSE;
		}

		$this->services[get_class($service)] = $service;

		return $this;
	}

	/**
	 * Registers service providers
	 *
	 * @param ServiceProvider[] $services
	 *
	 * @return $this
	 */
	public function registerServices(array $services)
	{
		foreach ($services as $service)
		{
			$this->registerService($service);
		}

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function instance($identifier, $instance)
	{
		$this->instances[$identifier] = $instance;

		return $this;
	}

	/**
	 * Removes an instance
	 *
	 * @param string $identifier
	 *
	 * @return $this
	 */
	public function remove($identifier)
	{
		if (isset($this->instances[$identifier]))
		{
			unset($this->instances[$identifier]);
		}

		return $this;
	}

	/**
	 * Tries to get the resource from the currently loaded resources
	 *
	 * @param string $identifier
	 *
	 * @return Resource|null The found resource, or null if not found
	 */
	protected function getResource($identifier)
	{
		if (isset($this->resources[$identifier]))
		{
			return $this->resources[$identifier];
		}

		if (class_exists($identifier, true))
		{
			return $this->resources[$identifier] = new Resource($identifier);
		}

		return null;
	}

	/**
	 * Finds a resource identified by the identifier passed
	 *
	 * @param string $identifier
	 *
	 * @return Resource|null The found resource, or null if not found
	 */
	protected function findResource($identifier)
	{
		if (isset($this->resources[$identifier]))
		{
			return $this->resources[$identifier];
		}

		foreach ($this->services as $service)
		{
			/** @type ServiceProvider $service */
			if ($service->provides and in_array($identifier, $service->provides))
			{
				$service->provide($this);
				$service->provides = false;

				break;
			}
		}

		return $this->getResource($identifier);
	}

	/**
	 * Finds and returns a new instance of a resource
	 *
	 * @param string $identifier
	 *
	 * @return Resource The found resource
	 *
	 * @throws ResolveException If the resource cannot be found
	 */
	public function find($identifier)
	{
		if ( ! $resource = $this->findResource($identifier))
		{
			throw new ResolveException('Could not find resource: '.$identifier);
		}

		return $resource;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get($identifier, array $arguments = [])
	{
		// If we find a previously resolved instance
		if ($instance = $this->getInstance($identifier))
		{
			// Return it
			return $instance;
		}

		// Find the resource
		$resource = $this->find($identifier);

		// Resolve an instance
		$instance = $resource->resolve($this, $arguments);

		// Apply any supplied extensions
		$instance = $this->applyExtensions($identifier, $instance);

		// When the resource prefers to be Singleton
		if ($resource->preferSingleton)
		{
			// Store the instance
			$this->instances[$identifier] = $instance;
		}

		return $instance;
	}

	/**
	 * {@inheritdoc}
	 */
	public function factory($identifier, array $arguments = [])
	{
		// Find the resource
		$resource = $this->find($identifier);

		// Resolve an instance
		$instance = $resource->resolve($this, $arguments);

		// Apply any supplied extensions
		$instance = $this->applyExtensions($identifier, $instance);

		return $instance;
	}

	/**
	 * Attaches extensions to an identifier
	 *
	 * @param string          $identifier
	 * @param string|callable $extension  the generic extension, or a callable implementing the extension
	 *
	 * @return $this
	 */
	public function extend($identifier, $extension)
	{
		$this->extends[$identifier][] = $extension;

		return $this;
	}

	/**
	 * Defines a generic resource extension
	 *
	 * @param string  $identifier
	 * @param Closure $extension
	 *
	 * @return $this
	 */
	public function extension($identifier, Closure $extension)
	{
		$this->extensions[$identifier] = $extension;

		return $this;
	}

	/**
	 * Applies all defined extensions to the instance
	 *
	 * @param string $identifier
	 * @param mixed  $instance
	 *
	 * @return mixed
	 */
	public function applyExtensions($identifier, $instance)
	{
		if ( ! isset($this->extends[$identifier]))
		{
			return $instance;
		}

		foreach ($this->extends[$identifier] as $extension)
		{
			if (is_string($extension) and isset($this->extensions[$extension]))
			{
				$extension = $this->extensions[$extension];
			}

			if ( ! is_callable($extension))
			{
				throw new InvalidExtensionException('Extension for resource '.$identifier.' cannot be applied: not callable.');
			}

			if ($result = call_user_func($extension, $this, $instance))
			{
				$instance = $result;
			}
		}

		return $instance;
	}

	/**
	 * Retrieves a resolved instance
	 *
	 * @param string $identifier
	 *
	 * @return mixed|null
	 */
	protected function getInstance($identifier)
	{
		if (isset($this->instances[$identifier]))
		{
			return $this->instances[$identifier];
		}
	}

	public function has($identifier)
	{
		if ($this->getInstance($identifier) or $this->findResource($identifier))
		{
			return true;
		}

		return false;
	}

	/**
	 * Check if a resolved instance exists
	 *
	 * @param string $identifier
	 *
	 * @return boolean
	 */
	public function isInstance($identifier, $name = null)
	{
		if ($name !== null)
		{
			$identifier = $identifier.'::'.$name;
		}

		return isset($this->instances[$identifier]);
	}

	public function offsetExists($offset)
	{
		return $this->has($offset);
	}

	public function offsetGet($offset)
	{
		return $this->get($offset);
	}

	public function offsetSet($offset, $resource)
	{
		// register as singleton
		$this->bind($offset, $resource);
	}

	public function offsetUnset($offset)
	{
		$this->remove($offset);
	}
}
