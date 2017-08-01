<?php

namespace ResumeNext\Container\Entry;

use Interop\Container\ContainerInterface;
use ResumeNext\Container\ResolverInterface;

class AliasEntry implements ResolverInterface {

	/** @var string */
	protected $id;

	/**
	 * Constructor
	 *
	 * @param string $id
	 */
	public function __construct($id) {
		$this->id = $id;
	}

	public function resolve(ContainerInterface $container) {
		return $container->get($this->id);
	}

}

/* vi:set ts=4 sw=4 noet: */
