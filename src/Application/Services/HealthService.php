<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Config\ServiceRegistry;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;

final class HealthService {
    public function __construct(
        private LoggerInterface $logger,
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
            if ($service['status']['data']['status'] !== 'ok') {
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
            'service' => 'gateway-service',
            'version' => trim($version),
            'services' => $services,
        ];
    }

    /**
     *
     * @return array<string,mixed>
     */
    private function checkService(string $baseUrl) : array {
        try {
            $response = rtrim($baseUrl, '/')
                    |> (fn ($x) => sprintf('%s/health', $x))
                    |> (fn ($x) => $this->httpClient->request('GET', $x, ['http_errors' => false, ]));

            return $response->getStatusCode() === 200
                ? json_decode($response->getBody()->getContents(), true)
                : ['data' => ['status' => 'down', ], ];
        } catch (\Throwable $e) {
            $this->logger->error(
                sprintf('Error checking health of service at %s: %s', $baseUrl, $e->getMessage()),
            );

            return [
                'data' => [
                    'status' => 'down',
                ],
            ];
        }
    }
}
