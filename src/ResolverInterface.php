<?php

namespace ResumeNext\Container;

use Interop\Container\ContainerInterface;

interface ResolverInterface {

	/**
	 * @param \Psr\Container\ContainerInterface $container
	 *
	 * @throws \ResumeNext\Container\Exception\ExceptionInterface
	 *
	 * @return mixed The resolved entry
	 */
	public function resolve(ContainerInterface $container);

}

/* vi:set ts=4 sw=4 noet: */
