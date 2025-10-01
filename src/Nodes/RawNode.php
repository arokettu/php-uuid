<?php

/**
 * @copyright 2023 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Uuid\Nodes;

final readonly class RawNode extends AbstractNode
{
    protected function assertValid(string $hex): void
    {
        // always valid
    }
}
