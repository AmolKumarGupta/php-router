<?php

namespace Amol\Router;

use Amol\Router\Contract\Http\RequestInterface;
use Amol\Router\Contract\Routing\DispatchInterface;
use Amol\Router\Exception\NoUrlPathException;
use Amol\Router\Routing\Dispatcher;
use Amol\Router\Routing\Groups;
use Amol\Router\Routing\Matcher;
use Amol\Router\Routing\Parameters;
use BadMethodCallException;
use Closure;

/**
 * @method Router prefix(string $uri) Prefix the given URI with the last prefix
 */
class Router
{
    /**
     * @var array<string, array<string, Closure|string[]>>
     */
    private array $routes = [];

    public function __construct(
        protected Parameters $parameters = new Parameters(),
        protected Matcher $matcher = new Matcher(),
        protected DispatchInterface $dispatcher = new Dispatcher(),
        protected Groups $groups = new Groups(),
    ) {
        $this->matcher->setParameter($this->parameters);
    }

    /**
     * @param array<string, mixed> $arguments
     */
    public function __call(string $name, array $arguments): Router
    {
        if ($this->groups->hasAttribute($name)) {
            $this->groups->record($name, ...$arguments);
            return $this;
        }

        $class = Router::class;
        throw new BadMethodCallException("Call to undefined method $class::$name");
    }

    public function group(Closure $callback): Router
    {
        $this->groups->pushGroupStack();
        $callback();
        $this->groups->popGroupStack();
        return $this;
    }

    /**
     * @param Closure|string[] $callback
     */
    public function match(string $method, string $route, Closure|array $callback): Router
    {
        $method = mb_strtoupper($method);

        if ($this->groups->hasGroups()) {
            /**
             * @var string $method
             * @var string $route
             * @var Closure|string[] $callback
             */
            [$method, $route, $callback] = $this->groups->handle($method, $route, $callback);
        }

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
            throw new NoUrlPathException();
        }

        $routes = $this->matcher->matchRoutes($component['path'], $this->routes);

        $fn = $routes[$request->getMethod()] ?? null;

        if (!$fn) {
            return;
        }

        $this->dispatcher->dispatch($this, $fn);
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
    public function allParameters(): array
    {
        return $this->parameters->all();
    }

}
