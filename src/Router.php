<?php

namespace Amol\Router;

use Amol\Router\Contract\Http\RequestInterface;
use Closure;
use Exception;
use ReflectionClass;

class Router
{
    /**
     * @var array<string, array<string, Closure|string[]>>
     */
    private array $routes = [];

    /**
     * @var string[]
     */
    private array $parameters = [];

    /**
     * @var string[]
     */
    private array $parameterValues = [];

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

            $reflectionObj = new ReflectionClass($obj);
            if (!$reflectionObj->hasMethod($methodName)) {
                throw new \RuntimeException("Method $methodName does not exist in class $className.");
            }

            $reflectionMethod = $reflectionObj->getMethod($methodName);
            $parameters = $reflectionMethod->getParameters();

            if (count($parameters) == 0) {
                $obj->$methodName();
                return;
            }

            $dependences = [];
            $parameterData = $this->getParameters();

            foreach ($parameters as $parameter) {
                $name = $parameter->getName();

                if (isset($parameterData[$name])) {
                    $dependences[$name] = $parameterData[$name];
                    continue;
                }

                if ($parameter->isDefaultValueAvailable()) {
                    $default = $parameter->getDefaultValue();
                    $dependences[$name] = $default;
                }
            }

            $obj->$methodName(...$dependences);
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
     * @return array<string, mixed>
     */
    private function getParameters(): array
    {
        $data = [];

        foreach ($this->parameters as $index => $parameter) {
            $data[$parameter] = $this->parameterValues[$index];
        }

        return $data;
    }

    /**
     * @return array<string, Closure|string[]>
     */
    private function matchRoutes(string $path): array
    {
        $path = rtrim($path, "/");

        foreach ($this->routes as $route => $values) {
            $route = rtrim($route, "/");

            if ($this->matchUri($route, $path)) {
                return $values;
            }
        }

        return [];
    }

    private function matchUri(string $route, string $path): bool
    {
        $patternedRoute = $this->createRegexPattern($route);

        if (count($this->parameters) == 0) {
            return $route == $path;
        }

        return $this->regexUri($patternedRoute, $path);
    }

    private function createRegexPattern(string $route): string
    {
        $matches = [];
        preg_match_all('@{(\w+)}@', $route, $matches);

        if (count($matches) < 1) {
            return $route;
        }

        $pattern = "([a-bA-Z0-9\w\-\+\%]+)";
        $this->parameters = $matches[1];
        return str_replace($matches[0], $pattern, $route);
    }

    private function regexUri(string $pattern, string $path): bool
    {
        $matches = [];
        $result = preg_match_all("@$pattern@", $path, $matches, PREG_SET_ORDER, 0);

        if ($result === false) {
            return false;
        }

        if (count($matches) < 1) {
            return true;
        }

        $data = $matches[0] ?? [];
        array_shift($data);

        $this->parameterValues = $data;
        return true;
    }

}
