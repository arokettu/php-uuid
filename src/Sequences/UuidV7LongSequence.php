<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Sequences;

use Arokettu\Clock\SystemClock;
use Arokettu\Uuid\UuidV7;
use Generator;
use Psr\Clock\ClockInterface;
use Random\Engine\PcgOneseq128XslRr64;
use Random\Randomizer;

/**
 * @implements UuidSequence<UuidV7>
 */
final readonly class UuidV7LongSequence implements UuidSequence
{
    private Inner\V7LongHexSequence $innerSeq;

    public function __construct(
        ClockInterface $clock = new SystemClock(),
        Randomizer $randomizer = new Randomizer(new PcgOneseq128XslRr64()),
    ) {
        $this->innerSeq = new Inner\V7LongHexSequence(true, $clock, $randomizer);
    }

    public function next(): UuidV7
    {
        return new UuidV7($this->innerSeq->next());
    }

    public function getIterator(): Generator
    {
        while (true) {
            yield $this->next();
        }
    }
}
