<?php

namespace Amol\Router;

use Amol\Router\Contract\Http\RequestInterface;
use Closure;

class Router
{
    /**
     * @var array<string, array<string, Closure>>
     */
    private array $routes = [];

    public function match(string $method, string $route, Closure $callback): void
    {
        $method = mb_strtoupper($method);

        $this->routes[$method][$route] = $callback;
    }

    public function dispatch(RequestInterface $request): void
    {
    }

    public function find(string $method, string $routePattern): Closure|null
    {
        return $this->routes[$method][$routePattern] ?? null;
    }

}
