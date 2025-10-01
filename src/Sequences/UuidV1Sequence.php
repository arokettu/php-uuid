<?php

/**
 * @copyright 2023 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Uuid\Sequences;

use Arokettu\Clock\SystemClock;
use Arokettu\Uuid\ClockSequences\ClockSequence;
use Arokettu\Uuid\Nodes;
use Arokettu\Uuid\UuidV1;
use Generator;
use Psr\Clock\ClockInterface;
use Random\Engine\PcgOneseq128XslRr64;
use Random\Randomizer;

/**
 * @implements UuidSequence<UuidV1>
 */
final readonly class UuidV1Sequence implements UuidSequence
{
    private Inner\V1HexSequence $innerSeq;

    public function __construct(
        Nodes\Node|null $node = null,
        int|ClockSequence|null $clockSequence = null,
        ClockInterface $clock = new SystemClock(),
        Randomizer $randomizer = new Randomizer(new PcgOneseq128XslRr64()),
    ) {
        $node ??= Nodes\StaticNode::random($randomizer);
        $clockSequence = match ($clockSequence) {
            ClockSequence::Random => null, // random for every request
            null => $randomizer->getInt(0, 0x3fff), // static random
            default => $clockSequence, // set
        };
        $this->innerSeq = new Inner\V1HexSequence(false, $node, $clockSequence, $clock, $randomizer);
    }

    public function next(): UuidV1
    {
        return new UuidV1($this->innerSeq->next());
    }

    public function getIterator(): Generator
    {
        while (true) {
            yield $this->next();
        }
    }
}
