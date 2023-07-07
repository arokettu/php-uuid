<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use UnexpectedValueException;

final readonly class Ulid extends AbstractUuid implements TimeBasedUuid
{
    use Helpers\UlidLikeDateTime;

    protected function assertValid(string $hex): void
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
            Helpers\UuidBytes::getVariant($this->hex) === 1 &&
            Helpers\UuidBytes::getVersion($this->hex) === 7;
    }

    /**
     * @psalm-api
     */
    public function toUuidV7(bool $lossy = false): UuidV7
    {
        if ($this->isUuidV7Compatible()) {
            return new UuidV7($this->hex);
        }

        if ($lossy === false) {
            throw new UnexpectedValueException('This ULID cannot be converted to UUID v7 losslessly');
        }

        $hex = $this->hex;

        Helpers\UuidBytes::setVariant($hex, 1);
        Helpers\UuidBytes::setVersion($hex, 7);

        return new UuidV7($hex);
    }
}
