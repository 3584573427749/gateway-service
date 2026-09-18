<?php

declare(strict_types=1);

namespace App\Http\Actions\Health;

use App\Application\Services\HealthService;
use App\Http\Actions\Action;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

final class ShowHealthAction extends Action {
    public function __construct(
        LoggerInterface $logger,
        private HealthService $healthService,
    ) {
        parent::__construct($logger);
    }

    protected function action() : ResponseInterface {
        return $this->respondWithData(
            $this->healthService->getHealthStatus(),
        );
    }
}
