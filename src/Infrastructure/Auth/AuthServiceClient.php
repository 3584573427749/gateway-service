<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth;

use App\Application\Settings;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

final class AuthServiceClient {
    private Client $client;

    public function __construct(Settings $settings) {
        $baseUrl = rtrim($settings->get('AUTH_SERVICE_URL'), '/');

        $this->client = new Client([
            'base_uri' => $baseUrl,
            'timeout' => 3.0,
        ]);
    }

    public function validateServiceToken(string $token, string $serviceName):array {
        try {
            $response = $this->client->post('/validate-service-token', [
                'json' => [
                    'token' => $token,
                    'service' => $serviceName,
                ],
            ]);

            return json_decode((string)$response->getBody(), true);
        } catch (RequestException $e) {
            return ['valid' => false];
        }
    }
}