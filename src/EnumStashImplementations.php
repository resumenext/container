<?php

namespace ResumeNext\Container;

abstract class EnumStashImplementations {

	const STANDARD = ResolvingArray::class;
	const CACHED = CachedResolvingArray::class;

}

/* vi:set ts=4 sw=4 noet: */
