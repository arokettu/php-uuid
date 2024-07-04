<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Sequences;

use Arokettu\Clock\SystemClock;
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
        ClockInterface $clock = new SystemClock(),
        Randomizer $randomizer = new Randomizer(new PcgOneseq128XslRr64()),
    ) {
        $node ??= Nodes\StaticNode::random($randomizer);
        $this->innerSeq = new Inner\V1HexSequence(false, $node, $clock, $randomizer);
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
