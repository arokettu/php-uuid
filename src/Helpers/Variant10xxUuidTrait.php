<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

use DomainException;

/**
 * @internal
 */
trait Variant10xxUuidTrait
{
    abstract public function getVersion(): int;

    final protected function assertValid(string $hex): void
    {
        if (UuidBytes::getVariant($hex) !== UuidVariant::v10xx) {
            throw new DomainException('The supplied UUID is not a valid RFC 9562 UUID');
        }
        if (UuidBytes::getVersion($hex) !== $this->getVersion()) {
            throw new DomainException(
                sprintf('The supplied UUID is not a valid RFC 9562 version %d UUID', $this->getVersion())
            );
        }
    }
}
