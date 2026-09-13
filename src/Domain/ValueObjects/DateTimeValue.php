<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use InvalidArgumentException;
use JsonSerializable;

final class DateTimeValue implements JsonSerializable {
    private DateTimeImmutable $value;

    /**
     * Accepts:
     * - ISO-8601 string (any timezone)
     * - DateTimeInterface (any timezone)
     *
     * Internally everything is stored as UTC.
     */
    public function __construct(string|DateTimeInterface $value) {
        if ($value instanceof DateTimeInterface) {
            $this->value = new DateTimeImmutable($value->format(DateTimeImmutable::ATOM));
            $this->value = $this->value->setTimezone(new \DateTimeZone('Europe/Mariehamn'));

            return;
        }

        try {
            $dt = new DateTimeImmutable($value);
        } catch (Exception) {
            throw new InvalidArgumentException("Invalid datetime value: {$value}");
        }

        $this->value = $dt->setTimezone(new \DateTimeZone('UTC'));
    }

    public static function fromString(string $value): self {
        return new self($value);
    }

    public function toDateTimeImmutable(): DateTimeImmutable {
        return $this->value;
    }

    public function toString(): string {
        return $this->value->format('Y-m-d H:i:s');
    }

    public function __toString(): string {
        return $this->toString();
    }

    public function jsonSerialize(): string {
        return $this->toString();
    }

    public function equals(self $other): bool {
        return $this->value->getTimestamp() === $other->value->getTimestamp();
    }
}