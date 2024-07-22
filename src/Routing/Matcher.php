<?php

namespace Amol\Router\Routing;

use Closure;

class Matcher
{
    private Parameters $parameters;

    public function setParameter(Parameters $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * @param array<string, array<string, Closure|string[]>> $routes
     * @return array<string, Closure|string[]>
     */
    public function matchRoutes(string $path, array $routes): array
    {
        $path = rtrim($path, "/");

        foreach ($routes as $route => $values) {
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

        if ($this->parameters->count() == 0) {
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

        $this->parameters->addKeys($matches[1]);
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

        $this->parameters->addValues($data);
        return true;
    }

}
