<?php

namespace ResumeNext\Container;

use Closure;
use Interop\Container\ServiceProvider;
use Iterator;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use stdClass;

class Builder {

	/** @var \stdClass */
	protected $stash;

	/** @var string */
	protected $containerClass;

	/** @var string */
	protected $stashClass;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->reset();
	}

	/**
	 * Add new alias entry
	 *
	 * @param string $alias Alias identifier
	 * @param string $id    Identifier
	 *
	 * @return $this
	 */
	public function addAlias(string $alias, string $id) {
		return $this->setEntry(
			$alias,
			$this->createObject(Entry\AliasEntry::class, $id)
		);
	}

	/**
	 * Add new callable entry
	 *
	 * @param string   $id        Identifier
	 * @param callable $callable
	 * @param array    $arguments
	 *
	 * @return $this
	 */
	public function addCallable(string $id, callable $callable, array $arguments = []) {
		return $this->setEntry($id, $this->createObject(
			Entry\CallableEntry::class,
			$callable,
			$arguments
		));
	}

	/**
	 * Add new class entry
	 *
	 * @param string      $id        Identifier
	 * @param string|null $className Fully qualified class name
	 * @param array       $arguments Argument list
	 *
	 * @return $this
	 */
	public function addClass(string $id, string $className = null, array $arguments = []) {
		$className = $className ?: $id;

		return $this->setEntry($id, $this->createObject(
			Entry\ClassEntry::class,
			$className,
			$arguments
		));
	}

	/**
	 * Add new Closure entry
	 *
	 * Closure will be bound to Container.
	 *
	 * @param string   $id        Identifier
	 * @param \Closure $closure   Instance of Closure
	 * @param array    $arguments Argument list
	 *
	 * @return $this
	 */
	public function addClosure(string $id, Closure $closure, array $arguments = []) {
		return $this->setEntry($id, $this->createObject(
			Entry\ClosureEntry::class,
			$closure,
			$arguments
		));
	}

	/**
	 * Add new delegate entry
	 *
	 * @param string                            $id        Identifier
	 * @param \Psr\Container\ContainerInterface $container Instance of ContainerInterface
	 *
	 * @return $this
	 */
	public function addDelegate(string $id, PsrContainerInterface $container) {
		return $this->setEntry($id, $this->createObject(
			Entry\DelegateEntry::class,
			$container,
			$id
		));
	}

	/**
	 * Add new factory entry
	 *
	 * @param string $id        Identifier
	 * @param string $className Name of class that implements FactoryInterface
	 * @param mixed  $config    Configuration
	 *
	 * @return $this
	 */
	public function addFactory(string $id, string $className, $config = null) {
		return $this->setEntry($id, $this->createObject(
			Entry\FactoryEntry::class,
			$className,
			$id,
			$config
		));
	}

	/**
	 * Add new Iterator entry
	 *
	 * @param string    $id       Identifier
	 * @param \Iterator $iterator Instance of Iterator
	 *
	 * @return $this
	 */
	public function addIterator(string $id, Iterator $iterator) {
		return $this->setEntry($id, $this->createObject(
			Entry\IteratorEntry::class,
			$iterator
		));
	}

	/**
	 * Add entries from a ServiceProvider
	 *
	 * @param \Interop\Container\ServiceProvider $serviceProvider
	 *
	 * @return $this
	 */
	public function addServiceProvider(ServiceProvider $serviceProvider) {
		$services = $serviceProvider->getServices();

		if (!is_array($services)) {
			throw new Exception\RuntimeException(sprintf(
				"Invalid return type \"%s\"",
				gettype($services)
			));
		}

		foreach ($services as $id => $service) {
			$id = (string)$id;

			$this->addService(
				$id,
				$service,
				$this->hasEntry($id) ? $this->getEntry($id) : null
			);
		}

		return $this;
	}

	/**
	 * Add new value entry
	 *
	 * @param string $id    Identifier
	 * @param mixed  $value Anything
	 *
	 * @return $this
	 */
	public function addValue(string $id, $value) {
		return $this->setEntry($id, $this->createObject(
			Entry\ValueEntry::class,
			$value
		));
	}

	/**
	 * Build Container instance from specified configuration
	 *
	 * @param array                              $config
	 * @param string|null                        $containerClass
	 * @param string|null                        $stashClass
	 * @param \ResumeNext\Container\Builder|null $builder        For testing
	 *
	 * @return \ResumeNext\Container\ContainerInterface
	 */
	public static function fromConfig(
		array $config,
		string $containerClass = null,
		string $stashClass = null,
		Builder $builder = null
	) {
		$builder = $builder ?: new static();
		$containerClass = $containerClass ?: $builder->containerClass;
		$stashClass = $stashClass ?: $builder->stashClass;

		return $builder
			->parseConfig($config)
			->setContainerClass($containerClass)
			->setStashClass($stashClass)
			->getContainer();
	}

	/**
	 * Create instance of Container with current entries
	 *
	 * The builder will reset to its initial state.
	 *
	 * @return \ResumeNext\Container\ContainerInterface
	 */
	public function getContainer() {
		$stashClass = $this->stashClass;
		$containerClass = $this->containerClass;

		$stash = $this->createObject($stashClass);
		$container = $this->createObject($containerClass, $stash);

		$stash->setContainer($container);
		$stash->setStash($this->stash);

		$this->reset();

		return $container;
	}

	/**
	 * @param string $id Identifier
	 *
	 * @return \ResumeNext\Container\ResolverInterface|bool
	 */
	public function getEntry($id) {
		if ($this->hasEntry($id)) {
			return $this->stash->$id;
		}

		return false;
	}

	/**
	 * Checks if Identifer has a given entry
	 *
	 * @param string $id Identifier
	 *
	 * @return bool TRUE if entry exists, FALSE if it doesn't exist
	 */
	public function hasEntry($id): bool {
		return property_exists($this->stash, $id);
	}

	/**
	 * Change the Container implementation
	 *
	 * @param string $className Must implement ContainerInterface
	 *
	 * @return $this
	 */
	public function setContainerClass(string $className) {
		if (!is_subclass_of($className, ContainerInterface::class)) {
			throw new Exception\LogicException(sprintf(
				"Invalid class name \"%s\"",
				$className
			));
		}

		$this->containerClass = $className;

		return $this;
	}

	/**
	 * Change the StashInterface implementation
	 *
	 * @param string $className Must implement StashInterface
	 *
	 * @return $this
	 */
	public function setStashClass(string $className) {
		if (!is_subclass_of($className, StashInterface::class)) {
			throw new Exception\LogicException(sprintf(
				"Invalid class name \"%s\"",
				$className
			));
		}

		$this->stashClass = $className;

		return $this;
	}

	/**
	 * Add new Service entry
	 *
	 * @param string                                           $id       Identifier
	 * @param callable                                         $service
	 * @param \ResumeNext\Container\ResolverInterface|null $previous
	 *
	 * @return $this
	 */
	protected function addService(string $id, callable $service, ResolverInterface $previous = null) {
		return $this->setEntry($id, $this->createObject(
			Entry\ServiceEntry::class,
			$service,
			$previous
		));
	}

	/**
	 * Invokes the new operator
	 *
	 * @param string $class
	 *
	 * @return object
	 */
	protected function createObject($class, ...$args) {
		return new $class(...$args);
	}

	/**
	 * Add entries as defined by configuration
	 *
	 * @param array[] $config
	 *
	 * @return $this
	 */
	protected function parseConfig(array $config) {
		if (array_key_exists("aliases", $config)) {
			assert(is_array($config["aliases"]));

			foreach ($config["aliases"] as $id => $alias) {
				$this->addAlias($id, $alias);
			}
		}

		if (array_key_exists("callables", $config)) {
			assert(is_array($config["callables"]));

			foreach ($config["callables"] as $id => $callable) {
				$this->addCallable(
					$id,
					...$this->parseEntryConfig($callable, "callable", "arguments")
				);
			}
		}

		if (array_key_exists("classes", $config)) {
			assert(is_array($config["classes"]));

			foreach ($config["classes"] as $id => $class) {
				$this->addClass(
					$id,
					...$this->parseEntryConfig($class, "name", "arguments")
				);
			}
		}

		if (array_key_exists("closures", $config)) {
			assert(is_array($config["closures"]));

			foreach ($config["closures"] as $id => $closure) {
				$this->addClosure(
					$id,
					...$this->parseEntryConfig($closure, "closure", "arguments")
				);
			}
		}

		if (array_key_exists("delegates", $config)) {
			assert(is_array($config["delegates"]));

			foreach ($config["delegates"] as $id => $container) {
				$this->addDelegate($id, $container);
			}
		}

		if (array_key_exists("factories", $config)) {
			assert(is_array($config["factories"]));

			foreach ($config["factories"] as $id => $factory) {
				$this->addFactory(
					$id,
					...$this->parseEntryConfig($factory, "name", "config")
				);
			}
		}

		if (array_key_exists("iterators", $config)) {
			assert(is_array($config["iterators"]));

			foreach ($config["iterators"] as $id => $iterator) {
				$this->addIterator($id, $iterator);
			}
		}

		if (array_key_exists("service_providers", $config)) {
			assert(is_array($config["service_providers"]));

			foreach ($config["service_providers"] as $key => $serviceProvider) {
				if (!is_subclass_of($serviceProvider, ServiceProvider::class)) {
					throw new Exception\RuntimeException(sprintf(
						"Invalid ServiceProvider with key \"%s\"",
						(string)$key
					));
				}

				$this->addServiceProvider(
					is_string($serviceProvider)
						? $this->createObject($serviceProvider)
						: $serviceProvider
				);
			}
		}

		if (array_key_exists("values", $config)) {
			assert(is_array($config["values"]));

			foreach ($config["values"] as $id => $value) {
				$this->addValue($id, $value);
			}
		}

		return $this;
	}

	/**
	 * Sort $config according to specified keys
	 *
	 * @param mixed $config
	 *
	 * @return array Numeric array with values from $config
	 */
	protected function parseEntryConfig($config, ...$keys) {
		$ret = [];

		if (!is_array($config)) {
			$ret[] = $config;
		} else {
			foreach ($keys as $key) {
				if (array_key_exists($key, $config)) {
					$ret[] = $config[$key];
				}
			}
		}

		return $ret;
	}

	/**
	 * Restore initial state
	 *
	 * @return $this
	 */
	protected function reset() {
		return $this
			->setContainerClass(Container::class)
			->setStash(new stdClass())
			->setStashClass(ResolvingArray::class);
	}

	/**
	 * Set an entry
	 *
	 * Will override previous entry if Identifier already exists
	 *
	 * @param string                                      $id    Identifier
	 * @param \ResumeNext\Container\ResolverInterface $entry Instance of ResolverInterface
	 *
	 * @return $this
	 */
	protected function setEntry(string $id, ResolverInterface $entry) {
		$this->stash->$id = $entry;

		return $this;
	}

	/**
	 * Change the stash instance
	 *
	 * @param object $stash
	 *
	 * @return $this
	 */
	protected function setStash($stash) {
		assert(is_object($stash));

		$this->stash = $stash;

		return $this;
	}

}

/* vi:set ts=4 sw=4 noet: */
