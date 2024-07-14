<?php

namespace Amol\Router\Contract\Http;

interface RequestInterface
{
    public function __construct(
        string $method,
        string $uri,
        string $host
    );

    public function getMethod(): string;

    public function getUri(): string;

}
