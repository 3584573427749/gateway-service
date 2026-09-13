<?php

declare(strict_types=1);

use App\Http\Actions\User\CreateUserAction;
use App\Http\Actions\User\DeleteUserAction;
use App\Http\Actions\User\GetAllUsersAction;
use App\Http\Actions\User\GetUserAction;
use App\Http\Actions\User\UpdateUserAction;
use Slim\App;

return function (App $app) : void {

    $app->post('/users', CreateUserAction::class);
    $app->get('/users', GetAllUsersAction::class);
    $app->get('/users/{id}', GetUserAction::class);
    $app->put('/users/{id}', UpdateUserAction::class);
    $app->delete('/users/{id}', DeleteUserAction::class);
};
