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

    public function match(string $method, string $route, Closure $callback): Router
    {
        $method = mb_strtoupper($method);
        $this->routes[$method][$route] = $callback;

        return $this;
    }

    public function get(string $route, Closure $callback): Router
    {
        return $this->match("get", $route, $callback);
    }

    public function post(string $route, Closure $callback): Router
    {
        return $this->match("post", $route, $callback);
    }

    public function put(string $route, Closure $callback): Router
    {
        return $this->match("put", $route, $callback);
    }

    public function delete(string $route, Closure $callback): Router
    {
        return $this->match("delete", $route, $callback);
    }

    public function dispatch(RequestInterface $request): void
    {
    }

    public function find(string $method, string $routePattern): Closure|null
    {
        return $this->routes[$method][$routePattern] ?? null;
    }

}
