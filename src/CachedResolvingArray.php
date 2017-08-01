<?php

namespace ResumeNext\Container;

class CachedResolvingArray extends ResolvingArray {

	/** @var array */
	protected $cache = [];

	public function offsetGet($offset) {
		if (!isset($this->cache[$offset])) {
			$this->cache[$offset] = parent::offsetGet($offset);
		}

		return $this->cache[$offset];
	}

	public function offsetSet($offset, $value) {
		parent::offsetSet($offset, $value);

		unset($this->cache[$offset]);
	}

	public function offsetUnset($offset) {
		unset($this->cache[$offset]);

		parent::offsetUnset($offset);
	}

}

/* vi:set ts=4 sw=4 noet: */
