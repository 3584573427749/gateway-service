<?php

declare(strict_types=1);


use Monolog\Handler\StreamHandler;
use Monolog\Logger;

return function () {
    $logger = new Logger('slim');
    $logger->pushHandler(new StreamHandler(__DIR__ . '/../logs/app.log'));
    return $logger;
};
