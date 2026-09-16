<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;
use JsonSerializable;

final class Email implements JsonSerializable {
    private string $value;

    public function __construct(string $value) {
        $clean = trim(strtolower($value));

        if (!filter_var($clean, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email address: {$value}");
        }

        $this->value = $clean;
    }

    public static function fromString(string $value) : self {
        return new self($value);
    }

    public function toString() : string {
        return $this->value;
    }

    public function __toString() : string {
        return $this->value;
    }

    public function jsonSerialize() : string {
        return $this->value;
    }

    public function equals(self $other) : bool {
        return $this->value === $other->value;
    }
}
