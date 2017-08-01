<?php

namespace ResumeNext\Container\Entry;

use Interop\Container\ContainerInterface;
use Iterator;
use ResumeNext\Container\Exception\RuntimeException;
use ResumeNext\Container\ResolverInterface;

class IteratorEntry implements ResolverInterface {

	/** @var \Iterator */
	protected $iterator;

	/**
	 * Constructor
	 *
	 * @param \Iterator $iterator
	 */
	public function __construct(Iterator $iterator) {
		$this->iterator = $iterator;
	}

	public function resolve(ContainerInterface $container) {
		$ret = null;

		if ($this->iterator->valid()) {
			$ret = $this->iterator->current();

			$this->iterator->next();
		} else {
			throw new RuntimeException(sprintf(
				"Invalid position of iterator \"%s\".",
				get_class($this->iterator)
			));
		}

		return $ret;
	}

}

/* vi:set ts=4 sw=4 noet: */
