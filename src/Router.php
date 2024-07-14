<?php

namespace Amol\Router;

use Amol\Router\Contract\Http\RequestInterface;
use Closure;
use Exception;

class Router
{
    /**
     * @var array<string, array<string, Closure>>
     */
    private array $routes = [];

    public function match(string $method, string $route, Closure $callback): Router
    {
        $method = mb_strtoupper($method);
        $this->routes[$route][$method] = $callback;

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
        $component = parse_url($request->getUri());

        if (!isset($component['path'])) {
            throw new Exception("url path is not found");
        }

        $routes = $this->matchRoutes($component['path']);

        $fn = $routes[$request->getMethod()] ?? null;

        if (!$fn) {
            return;
        }

        $fn();
    }

    public function find(string $method, string $routePattern): Closure|null
    {
        return $this->routes[$routePattern][$method] ?? null;
    }

    /**
     * @return array<string, Closure>
     */
    private function matchRoutes(string $path): array
    {
        $path = rtrim($path, "/");

        foreach ($this->routes as $route => $values) {
            $route = rtrim($route, "/");

            if ($route === $path) {
                return $values;
            }
        }

        return [];
    }

}
