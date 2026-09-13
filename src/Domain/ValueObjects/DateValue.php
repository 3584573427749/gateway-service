<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;
use JsonSerializable;
use DateTimeImmutable;

final class DateValue implements JsonSerializable {
    private DateTimeImmutable $value;

    public function __construct(string $value) {
        $clean = trim($value);

        // Kontrollera format (YYYY-MM-DD)
        if (!DateTimeImmutable::createFromFormat("Y-m-d", $clean)) {
            throw new InvalidArgumentException("Invalid date format: {$value}");
        }

        // Kontrollera att datumet faktiskt existerar
        if (DateTimeImmutable::createFromFormat("Y-m-d", $clean)->format("Y-m-d")!==$clean) {
            throw new InvalidArgumentException("Invalid date: {$value}");
        }

        $this->value = DateTimeImmutable::createFromFormat("Y-m-d", $clean);
    }

    public static function fromString(string $value):self {
        return new self($value);
    }

    public function toString():string {
        return $this->value->format('Y-m-d');
    }

    public function __toString():string {
        return $this->toString();
    }

    public function getValue():self {
        return $this->value;
    }

    public function jsonSerialize():string {
        return $this->toString();
    }

    public function equals(self $other):bool {
        return $this->value === $other->value;
    }
}