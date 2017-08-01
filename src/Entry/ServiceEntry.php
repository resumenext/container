<?php

namespace ResumeNext\Container\Entry;

use Interop\Container\ContainerInterface;
use ResumeNext\Container\ResolverInterface;

class ServiceEntry implements ResolverInterface {

	/** @var callable */
	protected $callable;

	/** @var \ResumeNext\Container\ResolverInterface */
	protected $previous;

	/** @var mixed */
	protected $product;

	/**
	 * Constructor
	 *
	 * @param callable                                         $callable
	 * @param \ResumeNext\Container\ResolverInterface|null $previous
	 */
	public function __construct(callable $callable, ResolverInterface $previous = null) {
		$this->callable = $callable;
		$this->previous = $previous;
	}

	/**
	 * Create callable to retrieve the previous ServiceProvider result
	 *
	 * @param \Interop\Container\ContainerInterface $container
	 *
	 * @return callable
	 */
	protected function createPreviousCallable(ContainerInterface $container): callable {
		return function() use ($container) {
			return $this->previous->resolve($container);
		};
	}

	public function resolve(ContainerInterface $container) {
		if ($this->callable !== null) {
			$this->product = call_user_func(
				$this->callable,
				$container,
				($this->previous === null) ? null : $this->createPreviousCallable($container)
			);

			$this->callable = $this->previous = null;
		}

		return $this->product;
	}

}

/* vi:set ts=4 sw=4 noet: */
