<?php

declare(strict_types=1);

use App\Application\Middleware\CorsMiddleware;
use Slim\App;

return function (App $app) : void {
    // JSON body parsing
    $app->addBodyParsingMiddleware();

    // CORS (ska ligga tidigt)
    $app->add(CorsMiddleware::class);
};
