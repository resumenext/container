<?php

namespace ResumeNext\Container\Entry;

use Interop\Container\ContainerInterface;
use ResumeNext\Container\{FactoryInterface, ResolverInterface};

class FactoryEntry implements ResolverInterface {

	/** @var string Implementation of FactoryInterface */
	protected $factoryInterface;

	/** @var string Name of class to be instantiated */
	protected $requestedName;

	/** @var mixed */
	protected $config;

	/**
	 * Constructor
	 *
	 * @param string $factoryInterface Implementation of FactoryInterface
	 * @param string $requestedName    Name of class to be instantiated
	 * @param mixed  $config           Optional configuration
	 */
	public function __construct(
		string $factoryInterface,
		string $requestedName,
		$config = null
	) {
		$this->factoryInterface = $factoryInterface;
		$this->requestedName = $requestedName;
		$this->config = $config;

		// Fail early
		assert(is_subclass_of(
			$this->factoryInterface,
			FactoryInterface::class
		));
	}

	public function resolve(ContainerInterface $container) {
		return call_user_func(
			[$this->factoryInterface, "create"],
			$container,
			$this->requestedName,
			$this->config
		);
	}

}

/* vi:set ts=4 sw=4 noet: */
