<?php

namespace ResumeNext\Container\Entry;

use Interop\Container\ContainerInterface;
use ResumeNext\Container\ResolverInterface;

class ClassEntry implements ResolverInterface {

	/** @var string */
	protected $className;

	/** @var array */
	protected $arguments;

	/**
	 * Constructor
	 *
	 * @param string $className
	 * @param array  $arguments
	 */
	public function __construct(string $className, array $arguments = []) {
		$this->className = $className;
		$this->arguments = $arguments;
	}

	public function resolve(ContainerInterface $container) {
		$class = $this->className;
		$argv = $this->arguments;

		return new $class(...$argv);
	}

}

/* vi:set ts=4 sw=4 noet: */
