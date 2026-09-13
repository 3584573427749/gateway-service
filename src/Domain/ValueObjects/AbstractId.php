<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;
use JsonSerializable;
use Ramsey\Uuid\Uuid;

abstract class AbstractId implements JsonSerializable {
    /**
     * @var string
     */
    private string $value;

    /**
     * Creates a new ID.
     *
     * If $value is null → generate new UUID v7
     * If $value is provided → must be a valid UUID (any version)
     */
    public function __construct(?string $value = null) {
        if ($value === null) {
            // Generate UUID v7 by default
            $this->value = Uuid::uuid7()->toString();

            return;
        }

        // Validate incoming UUID string (any valid UUID version)
        if (!Uuid::isValid($value)) {
            throw new InvalidArgumentException(
                sprintf("Invalid UUID value '%s' for ID of type %s", $value, static::class)
            );
        }

        $this->value = strtolower($value);
    }

    /**
     * Construct an ID from existing string (e.g. from database).
     */
    public static function fromString(string $value):static {
        return new static($value);
    }

    /**
     * Generates a new ID using UUID v7.
     */
    public static function random():static {
        return new static(null);
    }

    /**
     * Strict equality check:
     * - IDs must be the same concrete class
     * - and have identical UUID values
     */
    public function equals(self $other):bool {
        if (get_class($this) !== get_class($other)) {
            throw new InvalidArgumentException(sprintf(
                "Cannot compare %s with %s",
                get_class($this),
                get_class($other)
            ));
        }

        return $this->value === $other->value;
    }

    /**
     * Returns the raw UUID string.
     */
    public function toString():string {
        return $this->value;
    }

    /**
     * Magic cast to string.
     */
    public function __toString():string {
        return $this->value;
    }

    /**
     * JSON serialization returns the raw string.
     */
    public function jsonSerialize():string {
        return $this->value;
    }
}