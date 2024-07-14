<?php

namespace Amol\Router\Http;

use Amol\Router\Contract\Http\RequestInterface;
use Amol\Router\Http\Trait\CreateFromGlobals;

class Request implements RequestInterface
{
    use CreateFromGlobals;

    public function __construct(
        private readonly string $method,
        private readonly string $uri,
        private readonly string $host = "localhost"
    ) {
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getHost(): string
    {
        return $this->host;
    }

}
