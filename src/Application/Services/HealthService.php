<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Config\ServiceRegistry;
use GuzzleHttp\ClientInterface;
use Throwable;

final class HealthService {
    public function __construct(
        private ClientInterface $httpClient,
        private ServiceRegistry $serviceRegistry,
    ) {
    }

    /**
     * @return array<string,mixed>
     */
    public function getHealthStatus() : array {
        $services = [];

        foreach ($this->serviceRegistry->all() as $service => $baseUrl) {
            $services[$service] = [
                'status' => $this->checkService($baseUrl),
            ];
        }

        $overallStatus = 'healthy';

        foreach ($services as $service) {
            if ($service['status'] !== 'up') {
                $overallStatus = 'degraded';
                break;
            }
        }

        return [
            'status' => $overallStatus,
            'services' => $services,
        ];
    }

    private function checkService(string $baseUrl) : string {
        try {
            $response = rtrim($baseUrl, '/')
                    |> (fn ($x) => sprintf('%s/health', $x, ))
                    |> (fn ($x) => $this->httpClient->request('GET', $x, ['http_errors' => false, ], ));

            return $response->getStatusCode() === 200
                ? 'up'
                : 'down';
        } catch (Throwable) {
            return 'down';
        }
    }
}
