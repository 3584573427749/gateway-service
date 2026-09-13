<?php

declare(strict_types=1);

namespace App\Http\Actions;

use JsonSerializable;

class ActionPayload implements JsonSerializable {
    public function __construct(
        private int $statusCode = 200,
        private mixed $data = null,
        private ?ActionError $error = null,
    ) {
    }

    public function getStatusCode() : int {
        return $this->statusCode;
    }

    public function getData() : mixed {
        return $this->data;
    }

    public function getError() : ?ActionError {
        return $this->error;
    }

    /**
     *  @return array<string, mixed>
     */
    public function jsonSerialize() : array {
        $payload = [
           'statusCode' => $this->statusCode,
        ];

        if ($this->data !== null) {
            $payload['data'] = $this->data;
        } elseif ($this->error !== null) {
            $payload['error'] = $this->error;
        }

        return $payload;
    }
}
