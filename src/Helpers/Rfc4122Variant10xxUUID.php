<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

use DomainException;

/**
 * @internal
 */
trait Rfc4122Variant10xxUUID
{
    abstract public function getRfc4122Version(): int;

    final protected function assertValid(string $hex): void
    {
        if (UuidBytes::getVariant($hex) !== UuidVariant::RFC4122) {
            throw new DomainException('The supplied UUID is not a valid RFC 4122 UUID');
        }
        if (UuidBytes::getVersion($hex) !== $this->getRfc4122Version()) {
            throw new DomainException(
                sprintf('The supplied UUID is not a valid RFC 4122 version %d UUID', $this->getRfc4122Version())
            );
        }
    }
}
