<?php

declare(strict_types=1);

// bootstrap the application
require '../bootstrap/bootstrap.php';

// create server request from globals, handle it via the middleware queue
// and emit a response
$app->emit(
    $app->handle(
        $app->createServerRequestFromGlobals()
    )
);
