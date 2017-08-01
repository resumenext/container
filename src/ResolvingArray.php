<?php

namespace ResumeNext\Container;

use ArrayAccess;
use Interop\Container\ContainerInterface;

class ResolvingArray implements ArrayAccess, StashInterface {

	/** @var object */
	protected $stash;

	/** @var \Interop\Container\ContainerInterface */
	protected $container;

	public function offsetExists($offset): bool {
		return property_exists($this->stash, $offset);
	}

	/**
	 * @param string $offset
	 *
	 * @throws \ResumeNext\Container\Exception\OutOfBoundsException
	 *
	 * @return mixed
	 */
	public function offsetGet($offset) {
		$offset = (string)$offset;

		if ($this->offsetExists($offset)) {
			$entry = $this->stash->$offset;

			assert(is_a($entry, ResolverInterface::class));

			return $entry->resolve($this->container);
		}

		throw new Exception\OutOfBoundsException(sprintf(
			"Invalid key \"%s\".",
			$offset
		));
	}

	/**
	 * @param string $offset
	 *
	 * @throws \ResumeNext\Container\Exception\InvalidArgumentException
	 *
	 * @return void
	 */
	public function offsetSet($offset, $value) {
		if ($offset === null) {
			throw new Exception\InvalidArgumentException(
				"Offset must not be null"
			);
		}

		$offset = (string)$offset;

		$this->stash->$offset = $value;
	}

	public function offsetUnset($offset) {
		unset($this->stash->$offset);
	}

	public function setContainer(ContainerInterface $container) {
		$this->container = $container;

		return $this;
	}

	public function setStash($stash) {
		if (!is_object($stash)) {
			throw new Exception\InvalidArgumentException(sprintf(
				"Invalid argument type \"%s\"",
				gettype($stash)
			));
		}

		$this->stash = $stash;

		return $this;
	}

}

/* vi:set ts=4 sw=4 noet: */
