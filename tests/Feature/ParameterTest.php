<?php

use Amol\Router\Router;

test("route with {id} parameter", function () {
    $id = 10;
    $this->mock("get", "https://www.example.com/create/{$id}");
    $request = $this->request();

    $router = new Router;
    $router->get("/create/{id}", [PageController::class, 'create']);
    $router->dispatch($request);

    expect(PageController::$output)->toBe($id);
});

test("route with multiple parameter with different order", function () {
    $user = 10;
    $post = "this-is-sample";
    $comment = 1024;

    $this->mock("put", "https://www.example.com/user/{$user}/post/{$post}/{$comment}");
    $request = $this->request();

    $router = new Router;
    $router->put("/user/{user}/post/{post}/{comment}", [PageController::class, 'updateComment']);
    $router->dispatch($request);

    expect(PageController::$user)->toBe($user);
    expect(PageController::$post)->toBe($post);
    expect(PageController::$comment)->toBe($comment);
});

class PageController
{
    public static $output = null;

    public static $user = null;
    public static $post = null;
    public static $comment = null;

    public function create(int $id, $text = "test") 
    {
        self::$output = $id;
    }

    public function updateComment(int $comment, string $post, int $user) 
    {
        self::$user = $user;
        self::$post = $post;
        self::$comment = $comment;
    }
}