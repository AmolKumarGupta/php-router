<?php

namespace Amol\Router\Exception;

use Exception;

class NoUrlPathException extends Exception
{
    public function __construct()
    {
        parent::__construct(message: "url path is not found");
    }

}
