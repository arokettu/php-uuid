<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use Arokettu\Clock\SystemClock;
use Generator;
use Psr\Clock\ClockInterface;
use Random\Engine\Secure;
use Random\Randomizer;

/**
 * @deprecated
 * @see \Arokettu\Uuid\Sequences\UuidV7Sequence
 */
final class UuidV7MonotonicSequence implements Sequences\UuidSequence
{
    private Sequences\UuidV7Sequence $innerSequence;

    public function __construct(
        bool $reserveHighestCounterBit = true,
        ClockInterface $clock = new SystemClock(),
        Randomizer $randomizer = new Randomizer(new Secure()),
    ) {
        $this->innerSequence = new Sequences\UuidV7Sequence($clock, $randomizer);
    }

    public function next(): Uuid
    {
        return $this->innerSequence->next();
    }

    public function getIterator(): Generator
    {
        yield from $this->innerSequence;
    }
}
