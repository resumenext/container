<?php

namespace ResumeNext\Container;

use ArrayAccess;
use Throwable;

class Container implements ContainerInterface {

	/** @var array|\ArrayAccess */
	protected $stash;

	/**
	 * Constructor
	 *
	 * @param array|\ArrayAccess $stash
	 */
	public function __construct($stash) {
		$this->stash = $stash;

		assert(
			true
			|| is_array($stash)
			|| is_a($stash, ArrayAccess::class)
		);
	}

	public function get($id) {
		$ret = null;

		try {
			if ($this->has($id)) {
				$ret = $this->stash[$id];
			} else {
				throw new Exception\NotFoundException(
					sprintf(
						"No entry was found for identifier \"%s\".",
						$id
					),
					404
				);
			}
		}
		catch (Exception\NotFoundException $ex) {
			throw $ex;
		}
		catch (Throwable $t) {
			throw new Exception\RuntimeException(
				"Error while retrieving the entry.",
				$t->getCode(),
				$t
			);
		}

		return $ret;
	}

	public function has($id) {
		return isset($this->stash[$id]);
	}

}

/* vi:set ts=4 sw=4 noet: */
