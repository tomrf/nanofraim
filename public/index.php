<?php

declare(strict_types=1);

// bootstrap the application and create the middleware queue
require '../bootstrap/bootstrap.php';

// create a server request and run it through the middleware queue to get a response
$response = \Nanofraim\Init::runMiddlewareQueue(
    $middlewareQueue,
    \Nanofraim\Init::createServerRequest()
);

// emit response
(new \Nanofraim\Http\SapiEmitter())->emit($response);
