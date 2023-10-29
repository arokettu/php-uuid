<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Node;

final readonly class RawNode extends AbstractNode
{
    protected function assertValid(string $hex): void
    {
        // always valid
    }
}
