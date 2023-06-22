<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use UnexpectedValueException;

final class Ulid extends Uuid implements TimeBasedUuid
{
    use Helpers\UlidLikeDateTime;

    protected function assertValid(string $bytes): void
    {
        // Ulid is always valid
    }

    public function toString(): string
    {
        return $this->toBase32();
    }

    /**
     * Can be converted to UUIDv7 losslessly
     */
    public function isUuidV7Compatible(): bool
    {
        return
            Helpers\UuidBytes::getVariant($this->bytes) === 1 &&
            Helpers\UuidBytes::getVersion($this->bytes) === 7;
    }

    public function toUuidV7(bool $lossy = false): UuidV7
    {
        if ($this->isUuidV7Compatible()) {
            return new UuidV7($this->bytes);
        }

        if ($lossy === false) {
            throw new UnexpectedValueException('This ULID cannot be converted to UUID v7 losslessly');
        }

        $bytes = $this->bytes;

        // set variant
        $bytes[8] = \chr(0b10 << 6 | \ord($bytes[8]) & 0b111111); // Variant 1: set the highest 2 bits to bin 10
        // set version
        $bytes[6] = \chr(0x7 << 4 | \ord($bytes[6]) & 0b1111); // Version 7: set the highest 4 bits to hex '7'

        return new UuidV7($bytes);
    }
}
