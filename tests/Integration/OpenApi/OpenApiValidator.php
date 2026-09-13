<?php

declare(strict_types=1);

namespace Tests\Integration\OpenApi;

use League\OpenAPIValidation\PSR7\OperationAddress;
use League\OpenAPIValidation\PSR7\ResponseValidator;
use League\OpenAPIValidation\PSR7\ServerRequestValidator;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class OpenApiValidator {
    private ResponseValidator $responseValidator;

    private ServerRequestValidator $requestValidator;

    public function __construct() {
        $builder = (new ValidatorBuilder())
            ->fromYamlFile(__DIR__ . '/../../../openapi.yaml');

        $this->responseValidator = $builder->getResponseValidator();
        $this->requestValidator = $builder->getServerRequestValidator();
    }

    public function validateResponse(
        string $path,
        string $method,
        ResponseInterface $response,
    ) : void {
        $this->responseValidator->validate(
            new OperationAddress($path, strtolower($method)),
            $response,
        );
    }

    public function validateRequest(
        ServerRequestInterface $request,
    ) : void {
        $this->requestValidator->validate($request);
    }
}
