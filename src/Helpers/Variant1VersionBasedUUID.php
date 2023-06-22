<?php

namespace Arokettu\Uuid\Helpers;

/**
 * @internal
 */
trait Variant1VersionBasedUUID
{
    abstract private function version(): int;

    protected function assertValid(string $bytes): void
    {
        if (UuidBytes::getVariant($bytes) !== 1) {
            throw new \ValueError("The supplied UUID is not a valid RFC 4122 UUID");
        }
        if (UuidBytes::getVersion($bytes) !== $this->version()) {
            throw new \ValueError(
                sprintf("The supplied UUID is not a valid RFC 4122 version %d UUID", $this->version())
            );
        }
    }
}
