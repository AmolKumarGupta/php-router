<?php

namespace Amol\Router\Routing;

class RouteGroup
{
    public function __construct(
        public string $prefix,
    ) {
    }

    public function __invoke(RouteGroup $model): void
    {
        $model->prefix .= $this->prefix;
    }

}
