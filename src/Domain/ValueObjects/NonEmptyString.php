<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;
use JsonSerializable;

final class NonEmptyString implements JsonSerializable {
    private string $value;

    public function __construct(string $value) {
        $clean = trim($value);

        if ($clean === '') {
            throw new InvalidArgumentException("String cannot be empty");
        }

        $this->value = $clean;
    }

    public static function fromString(string $value):self {
        return new self($value);
    }

    public function toString():string {
        return $this->value;
    }

    public function __toString():string {
        return $this->value;
    }

    public function jsonSerialize():string {
        return $this->value;
    }

    public function equals(self $other):bool {
        return $this->value === $other->value;
    }
}
