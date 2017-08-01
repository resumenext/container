<?php

namespace ResumeNext\Container\Exception;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends \OutOfBoundsException implements NotFoundExceptionInterface {
}

/* vi:set ts=4 sw=4 noet: */
