<?php

namespace Tests;

use Amol\Router\Contract\Http\RequestInterface;
use Amol\Router\Http\Request;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    
    public function mock(string $method, $url)
    {
        $method = mb_strtoupper($method);

        $components = parse_url($url);

        $_SERVER['HTTP_HOST'] = $components['host'];
        $_SERVER['REQUEST_URI'] = $components['path'] ?? "/";
        $_SERVER['REQUEST_METHOD'] = $method;
        
        if ( !empty($components['query']) ) {
            $_SERVER['REQUEST_URI'] .= '?' . ($components['query'] ?? '');
        }
    }

    public function request(): RequestInterface
    {
        return Request::createFromGlobals();
    }

}
