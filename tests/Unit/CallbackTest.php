<?php

use Amol\Router\Router;

test("using closure", function () {
    $this->mock("get", "https://www.example.com");

    $output = null;
    $router = new Router;
    $router->match('get', '/', function () use(&$output) {
        $output = "homepage";
    });
    
    $router->dispatch($this->request());

    expect($output)->toBe("homepage");
});

test("using controller", function () {
    $this->mock("post", "https://www.example.com/save");

    $router = new Router;
    $router->match('get', '/', [HomeController::class, 'index']);
    $router->match('post', '/save', [HomeController::class, 'save']);
    
    $router->dispatch($this->request());

    expect(HomeController::$output)->toBe("saved");
});

class HomeController 
{

    public static $output = null;

    public function save() 
    {
        self::$output = "saved";
    }
}