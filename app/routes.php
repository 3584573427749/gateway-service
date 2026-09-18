<?php

declare(strict_types=1);

use App\Http\Actions\Health\ShowHealthAction;
use App\Http\Actions\Proxy\ProxyAction;
use Slim\App;

return function (App $app) : void {

    $app->get('/health', ShowHealthAction::class);
    $app->map(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], '/api/{service}/{path:.*}', ProxyAction::class);
};
