<?php

namespace Amol\Router;

use Amol\Router\Contract\Http\RequestInterface;
use Closure;
use Exception;

class Router
{
    /**
     * @var array<string, array<string, Closure|string[]>>
     */
    private array $routes = [];

    /**
     * @param Closure|string[] $callback
     */
    public function match(string $method, string $route, Closure|array $callback): Router
    {
        $method = mb_strtoupper($method);
        $this->routes[$route][$method] = $callback;

        return $this;
    }

    /**
     * @param Closure|string[] $callback
     */
    public function get(string $route, Closure|array $callback): Router
    {
        return $this->match("get", $route, $callback);
    }

    /**
     * @param Closure|string[] $callback
     */
    public function post(string $route, Closure|array $callback): Router
    {
        return $this->match("post", $route, $callback);
    }

    /**
     * @param Closure|string[] $callback
     */
    public function put(string $route, Closure|array $callback): Router
    {
        return $this->match("put", $route, $callback);
    }

    /**
     * @param Closure|string[] $callback
     */
    public function delete(string $route, Closure|array $callback): Router
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

        if (is_array($fn)) {
            [$className, $methodName] = $fn;
            $obj = new $className();

            if (!method_exists($obj, $methodName)) {
                throw new \RuntimeException("Method $methodName does not exist in class $className.");
            }

            $obj->$methodName();
            return;
        }

        $fn();
    }

    /**
     * @return string[]
     */
    public function find(string $method, string $routePattern): Closure|array|null
    {
        return $this->routes[$routePattern][$method] ?? null;
    }

    /**
     * @return array<string, Closure|string[]>
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
