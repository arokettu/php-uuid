<?php

/**
 * @copyright 2023 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Uuid;

final readonly class GenericUuid extends AbstractUuid
{
    protected function assertValid(string $hex): void
    {
        // always valid
    }
}
