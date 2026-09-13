<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use cebe\openapi\exceptions\IOException;
use cebe\openapi\exceptions\TypeErrorException;
use cebe\openapi\exceptions\UnresolvableReferenceException;
use cebe\openapi\Reader;

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

try {
    $openapi = Reader::readFromYamlFile(__DIR__ . '/../openapi.yaml');

    $errors = $openapi->getErrors();

    if ($errors !== []) {
        foreach ($errors as $error) {
            fwrite(STDERR, $error . PHP_EOL);
        }

        exit(1);
    }
    echo "OpenAPI validation successful.\n";
} catch (IOException $e) {
    fwrite(STDERR, "Failed to parse OpenAPI file.\n");
    exit(1);
} catch (TypeErrorException $e) {
    fwrite(STDERR, "Type error found in OpenAPI file.\n");
    exit(1);
} catch (UnresolvableReferenceException $e) {
    fwrite(STDERR, "Unresolvable reference found in OpenAPI file.\n");
    exit(1);
}
