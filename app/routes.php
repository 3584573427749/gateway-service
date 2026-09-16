<?php

declare(strict_types=1);

use App\Http\Actions\Health\ShowHealthAction;
use Slim\App;

return function (App $app) : void {

    $app->get('/health', ShowHealthAction::class);
};
