<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

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
}
