<?php

namespace Smack\Di\Exception;

use \Interop\Container\Exception\NotFoundException as NotFoundExceptionInterface;

class NotFoundException extends \Exception implements NotFoundExceptionInterface
{
}