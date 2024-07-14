<?php

use Amol\Router\Router;

test("matches basic routes", function () {
    $router = new Router;

    $router->match('get', '/', fn() => "homepage");

    $fn = $router->find("GET", "/");
    expect($fn())->toBe("homepage");

    $fn = $router->find("GET", "/home");
    expect($fn)->toBe(null);
});


test("route with get method", function () {
    $router = new Router;
    $router->get('/home', fn() => "homepage");

    $fn = $router->find("GET", "/home");

    expect($fn())->toBe("homepage");
});


test("route with post method", function () {
    $router = new Router;
    $router->post('/save', fn() => "saved");

    $fn = $router->find("POST", "/save");

    expect($fn())->toBe("saved");
});


test("route with put method", function () {
    $router = new Router;
    $router->put('/update', fn() => "updated");

    $fn = $router->find("PUT", "/update");

    expect($fn())->toBe("updated");
});


test("route with delete method", function () {
    $router = new Router;
    $router->delete('/remove', fn() => "removed");

    $fn = $router->find("DELETE", "/remove");

    expect($fn())->toBe("removed");
});