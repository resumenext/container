<?php

namespace ResumeNext\Container\Entry;

use Interop\Container\ContainerInterface;
use ResumeNext\Container\ResolverInterface;

class ValueEntry implements ResolverInterface {

	/** @var mixed */
	protected $value;

	/**
	 * Constructor
	 *
	 * @param mixed $value
	 */
	public function __construct($value) {
		$this->value = $value;
	}

	public function resolve(ContainerInterface $container) {
		return $this->value;
	}

}

/* vi:set ts=4 sw=4 noet: */
