<?php

use Amol\Router\Http\Request;
use Amol\Router\Router;

test("matches basic routes", function () {
    $router = new Router;

    $router->match('get', '/', fn() => "homepage");

    $fn = $router->find("GET", "/");
    expect($fn())->toBe("homepage");

    $fn = $router->find("GET", "/home");
    expect($fn)->toBe(null);
});