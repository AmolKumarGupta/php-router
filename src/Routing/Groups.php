<?php

namespace Amol\Router\Routing;

use Amol\Router\Router;
use Closure;

class Groups
{
    /**
     * @var string[]
     */
    private array $attributes = [
        "prefix"
    ];

    /**
     * @var array<string, mixed>
     */
    private array $store = [];

    /**
     * @var array<RouteGroup>
     */
    private array $groupStack = [];

    public function hasAttribute(string $attribute): bool
    {
        return in_array($attribute, $this->attributes);
    }

    public function record(string $attribute, mixed $val): Groups
    {
        $this->store[$attribute] = $val;
        return $this;
    }

    public function hasGroups(): bool
    {
        return count($this->groupStack) > 0;
    }

    public function pushGroupStack(): void
    {
        $this->groupStack[] = new RouteGroup(...$this->store);
        $this->cleanUp();
    }

    public function popGroupStack(): void
    {
        array_shift($this->groupStack);
    }

    /**
     * remove all the items in store for current group stack
     */
    protected function cleanUp(): void
    {
        $this->store = [];
    }

    /**
     * @param Closure|string[] $callback
     *
     * @return mixed[]
     */
    public function handle(string $method, string $route, Closure|array $callback): array
    {
        $group = new RouteGroup(prefix: "");
        $this->pipeAndMutateGroup($group);

        $route = $group->prefix . $route;

        return [$method, $route, $callback];
    }

    public function pipeAndMutateGroup(RouteGroup $model): void
    {
        foreach($this->groupStack as $group) {
            $group($model);
        }
    }

}
