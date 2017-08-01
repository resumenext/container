<?php

namespace ResumeNext\Container\Entry;

use Interop\Container\ContainerInterface;
use ResumeNext\Container\ResolverInterface;

class CallableEntry implements ResolverInterface {

	/** @var callable */
	protected $callable;

	/** @var array */
	protected $arguments;

	/**
	 * Constructor
	 *
	 * @param callable $callable
	 * @param array    $arguments
	 */
	public function __construct(callable $callable, array $arguments = []) {
		$this->callable = $callable;
		$this->arguments = $arguments;
	}

	public function resolve(ContainerInterface $container) {
		return call_user_func_array($this->callable, $this->arguments);
	}

}

/* vi:set ts=4 sw=4 noet: */
