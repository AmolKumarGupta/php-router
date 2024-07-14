<?php

namespace Amol\Router\Http\Trait;

use Amol\Router\Contract\Http\RequestInterface;

trait CreateFromGlobals
{
    public static function createFromGlobals(): RequestInterface
    {
        return new static(
            $_SERVER['REQUEST_METHOD'],
            $_SERVER['REQUEST_URI'],
            $_SERVER['HTTP_HOST'],
        );
    }

}
