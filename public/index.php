<?php

declare(strict_types=1);

// bootstrap the application and get the middleware queue
require '../bootstrap/bootstrap.php';

// create a server request and run the middleware queue with Relay to get a response
$response = (new \Relay\Relay($middlewareQueue))
    ->handle(\Nanofraim\Init::createServerRequest())
;

// emit response
(new \Nanofraim\Http\SapiEmitter())->emit($response);
