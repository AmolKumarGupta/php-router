<?php

test("dispatch sample request", function () {
    $this->mock("get", "https://www.example.com/home?test=tty&label=1");
    $request = $this->request();

    expect($request->getMethod())->toBe("GET");
    expect($request->getUri())->toBe("/home?test=tty&label=1");
    expect($request->getHost())->toBe("www.example.com");
});