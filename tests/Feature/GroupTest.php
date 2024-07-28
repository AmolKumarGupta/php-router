<?php

use Amol\Router\Router;

test("routes with a group", function () {
    $this->mock("get", "https://www.example.com/users/profile");
    
    $router = new Router;

    $router->prefix("/users")->group(function () use($router) {
        $router->get("/profile", function () {
            $this->output = "profile";
        });
    });

    $router->dispatch($this->request());

    expect($this->output)->toBe("profile");
});