<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Config\ServiceRegistry;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProxyService {
    public function __construct(
        private ClientInterface $httpClient,
        private ServiceRegistry $serviceRegistry,
    ) {
    }

    public function forward(
        ServerRequestInterface $request,
        string $service,
        string $path,
    ) : ResponseInterface {
        $baseUrl = $this->serviceRegistry->get($service);

        $targetUrl = sprintf(
            '%s/%s',
            rtrim($baseUrl, '/'),
            ltrim($path, '/'),
        );

        /** @var array<string, mixed> $headers */
        $headers = $this->filterHeaders(
            $request->getHeaders(),
        );

        return $this->httpClient->request(
            $request->getMethod(),
            $targetUrl,
            [
                'headers' => $headers,
                'body' => (string)$request->getBody(),
                'http_errors' => false,
            ],
        );
    }

    /**
     * @param array<string, array<string>> $headers
     *
     * @return array<string, array<string>>
     */
    private function filterHeaders(array $headers) : array {
        foreach ($headers as $name => $values) {
            if (strtolower($name) === 'host') {
                unset($headers[$name]);
            }
        }

        return $headers;
    }
}
