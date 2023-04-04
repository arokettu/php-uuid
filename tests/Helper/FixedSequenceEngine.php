<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests\Helper;

use Random\Engine;

final class FixedSequenceEngine implements Engine
{
    public function __construct(
        private readonly string $bytes,
    ) {}

    public function generate(): string
    {
        return $this->bytes;
    }
}
