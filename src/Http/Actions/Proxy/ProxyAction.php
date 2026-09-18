<?php

declare(strict_types=1);

namespace App\Http\Actions\Proxy;

use App\Application\Services\ProxyService;
use App\Http\Actions\Action;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class ProxyAction extends Action {
    public function __construct(LoggerInterface $logger, private ProxyService $proxyService) {
        parent::__construct($logger);
    }

    protected function action() : ResponseInterface {
        return $this->proxyService->forward(
            $this->request,
            $this->args['service'],
            $this->args['path'] ?? '',
        );
    }
}
