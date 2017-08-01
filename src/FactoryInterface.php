<?php

namespace ResumeNext\Container;

use Psr\Container\ContainerInterface;

/**
 * Interface for a factory
 *
 * A factory is an abstract class which implements this
 * interface, so the container knows how to consume the
 * factory and get the product. Any type of product can
 * be created, including non-objects.
 */
interface FactoryInterface {

	/**
	 * Create anything
	 *
	 * @param \Psr\Container\ContainerInterface $container     Requesting ContainerInterface instance
	 * @param string                            $requestedName Identifier
	 * @param mixed                             $config        Configuration
	 *
	 * @return mixed
	 */
	public static function create(
		ContainerInterface $container,
		string $requestedName,
		$config = null
	);

}

/* vi:set ts=4 sw=4 noet: */
