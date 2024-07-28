<?php

namespace Amol\Router\Routing;

use Closure;
use ReflectionClass;
use RuntimeException;
use Amol\Router\Router;
use Amol\Router\Contract\Routing\DispatchInterface;

class Dispatcher implements DispatchInterface
{
    /**
     * @param Closure|string[] $callback
     */
    public function dispatch(Router $router, Closure|array $callback): void
    {
        $parameterData = $router->allParameters();

        if (!is_array($callback)) {
            $callback(...$parameterData);
            return;
        }

        [$className, $methodName] = $callback;
        $obj = new $className();

        $reflectionObj = new ReflectionClass($obj);
        if (!$reflectionObj->hasMethod($methodName)) {
            throw new RuntimeException("Method $methodName does not exist in class $className.");
        }

        $reflectionMethod = $reflectionObj->getMethod($methodName);
        $parameters = $reflectionMethod->getParameters();

        if (count($parameters) == 0) {
            $obj->$methodName();
            return;
        }

        $dependences = [];

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
    }

}
