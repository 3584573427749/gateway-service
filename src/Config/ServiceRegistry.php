<?php

declare(strict_types=1);

namespace App\Config;

use RuntimeException;

final class ServiceRegistry {
    /**
     * @param array<string,string> $services
     */
    public function __construct(private array $services) {
    }

    public function has(string $service) : bool {
        return isset($this->services[$service]);
    }

    public function get(string $service) : string {
        if (!$this->has($service)) {
            throw new RuntimeException(
                sprintf('Unknown service "%s"', $service),
            );
        }

        return $this->services[$service];
    }

    /**
     * @return string[]
     */
    public function all() : array {
        return $this->services;
    }
}
