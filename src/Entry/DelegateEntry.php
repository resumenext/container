<?php

namespace ResumeNext\Container\Entry;

use Interop\Container\ContainerInterface;
use ResumeNext\Container\ResolverInterface;

class DelegateEntry implements ResolverInterface {

	/** @var \Psr\Container\ContainerInterface */
	protected $container;

	/** @var string */
	protected $id;

	/**
	 * Constructor
	 *
	 * @param \Psr\Container\ContainerInterface $container
	 * @param string                            $id
	 */
	public function __construct($container, $id) {
		$this->container = $container;
		$this->id = $id;
	}

	public function resolve(ContainerInterface $container) {
		return $this->container->get($this->id);
	}

}

/* vi:set ts=4 sw=4 noet: */
