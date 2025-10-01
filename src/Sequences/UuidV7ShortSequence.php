<?php

/**
 * @copyright 2023 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

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
final readonly class UuidV7ShortSequence implements UuidSequence
{
    private Inner\V7ShortHexSequence $innerSeq;

    public function __construct(
        ClockInterface $clock = new SystemClock(),
        Randomizer $randomizer = new Randomizer(new PcgOneseq128XslRr64()),
    ) {
        $this->innerSeq = new Inner\V7ShortHexSequence($clock, $randomizer);
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
