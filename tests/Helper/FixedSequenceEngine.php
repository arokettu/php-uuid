<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests\Helper;

use Random\Engine;

final class FixedSequenceEngine implements Engine
{
    private readonly iterable $bytes;

    public function __construct(string ...$bytes)
    {
        $this->bytes = new \InfiniteIterator(new \ArrayIterator($bytes));
        $this->bytes->rewind();
    }

    public function generate(): string
    {
        $bytes = $this->bytes->current();
        $this->bytes->next();
        return $bytes;
    }
}
