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

        $overallStatus = 'ok';

        foreach ($services as $service) {
            if ($service['status'] !== 'up') {
                $overallStatus = 'degraded';
                break;
            }
        }

        $version = file_get_contents(dirname(__DIR__, 3) . '/VERSION');
        if ($version === false) {
            $version = 'unknown';
        }

        return [
            'status' => $overallStatus,
            'service' => 'gw-service',
            'version' => trim($version),
            'services' => $services,
        ];
    }

    private function checkService(string $baseUrl) : string {
        try {
            $response = rtrim($baseUrl, '/')
                    |> (fn ($x) => sprintf('%s/health', $x))
                    |> (fn ($x) => $this->httpClient->request('GET', $x, ['http_errors' => false, ]));

            return $response->getStatusCode() === 200
                ? 'up'
                : 'down';
        } catch (Throwable) {
            return 'down';
        }
    }
}
