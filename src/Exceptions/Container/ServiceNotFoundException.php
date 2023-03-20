<?php

namespace CraftyDigit\Puff\Exceptions\Container;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

class ServiceNotFoundException extends Exception implements NotFoundExceptionInterface
{}