<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

final readonly class GenericUuid extends Uuid
{
    protected function assertValid(string $bytes): void
    {
        // always valid
    }
}
