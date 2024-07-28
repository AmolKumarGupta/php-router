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


test("routes with deep group", function () {
    $id = "123";
    $this->mock("post", "https://www.example.com/users/profile/photo/save/$id");
    
    $router = new Router;

    $router->prefix("/users")->group(function () use($router) {
        $router->prefix("/profile")->group(function () use($router) {
            $router->prefix("/photo")->group(function () use($router) {
                $router->post("/save/{id}", function ($id) {
                    $this->output = $id;
                });
            });
        });
    });

    $router->dispatch($this->request());

    expect($this->output)->toBe($id);
});