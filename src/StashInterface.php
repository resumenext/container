<?php

namespace ResumeNext\Container;

use Interop\Container\ContainerInterface;

interface StashInterface {

	/**
	 * @param \Interop\Container\ContainerInterface $container
	 *
	 * @return $this
	 */
	public function setContainer(ContainerInterface $container);

	/**
	 * @param object $stash
	 *
	 * @return $this
	 */
	public function setStash($stash);

}

/* vi:set ts=4 sw=4 noet: */
