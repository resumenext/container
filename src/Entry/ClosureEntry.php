<?php

namespace ResumeNext\Container\Entry;

use Closure;
use Interop\Container\ContainerInterface;

class ClosureEntry extends CallableEntry {

	/**
	 * Constructor
	 *
	 * @param \Closure $closure
	 * @param array    $arguments
	 */
	public function __construct(Closure $closure, array $arguments = []) {
		$this->callable = $closure;
		$this->arguments = $arguments;
	}

	public function resolve(ContainerInterface $container) {
		return $this->callable->call($container, ...$this->arguments);
	}

}

/* vi:set ts=4 sw=4 noet: */
