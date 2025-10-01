<?php

/**
 * @copyright 2023 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Uuid\Sequences;

use Arokettu\Clock\SystemClock;
use Arokettu\Uuid\Ulid;
use Generator;
use Psr\Clock\ClockInterface;
use Random\Engine\PcgOneseq128XslRr64;
use Random\Randomizer;

/**
 * @implements UuidSequence<Ulid>
 */
final readonly class UlidSequence implements UuidSequence
{
    private Inner\V7LongHexSequence $innerSeq;

    public function __construct(
        bool $uuidV7Compatible = false,
        ClockInterface $clock = new SystemClock(),
        Randomizer $randomizer = new Randomizer(new PcgOneseq128XslRr64()),
    ) {
        $this->innerSeq = new Inner\V7LongHexSequence($uuidV7Compatible, $clock, $randomizer);
    }

    public function next(): Ulid
    {
        return new Ulid($this->innerSeq->next());
    }

    public function getIterator(): Generator
    {
        while (true) {
            yield $this->next();
        }
    }
}
