<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

/**
 * @internal
 */
trait Rfc4122Variant1UUID
{
    abstract public function getRfc4122Version(): int;

    final protected function assertValid(string $hex): void
    {
        if (UuidBytes::getVariant($hex) !== 1) {
            throw new \ValueError("The supplied UUID is not a valid RFC 4122 UUID");
        }
        if (UuidBytes::getVersion($hex) !== $this->getRfc4122Version()) {
            throw new \ValueError(
                sprintf("The supplied UUID is not a valid RFC 4122 version %d UUID", $this->getRfc4122Version())
            );
        }
    }
}
