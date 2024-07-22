<?php

namespace Amol\Router\Contract\Routing;

use Amol\Router\Router;
use Closure;

interface DispatchInterface
{
    /**
     * @param Closure|string[] $callback
     */
    public function dispatch(Router $router, Closure|array $callback): void;

}
