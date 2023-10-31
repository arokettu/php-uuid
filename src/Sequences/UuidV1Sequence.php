<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Sequences;

use Arokettu\Clock\SystemClock;
use Arokettu\Uuid\Nodes\Node;
use Arokettu\Uuid\Uuid;
use Arokettu\Uuid\UuidV1;
use Generator;
use Psr\Clock\ClockInterface;
use Random\Engine\Secure;
use Random\Randomizer;

/**
 * @implements UuidSequence<UuidV1>
 */
final class UuidV1Sequence implements UuidSequence
{
    private UuidV6Sequence $innerSequence;

    public function __construct(
        ?Node $node = null,
        ClockInterface $clock = new SystemClock(),
        Randomizer $randomizer = new Randomizer(new Secure()),
    ) {
        // V1 sequences should be very rare, so I don't want to optimize
        $this->innerSequence = new UuidV6Sequence($node, $clock, $randomizer);
    }

    public function next(): Uuid
    {
        return $this->innerSequence->next()->toUuidV1();
    }

    /**
     * @return Generator<int, UuidV1, void>
     */
    public function getIterator(): Generator
    {
        while (true) {
            yield $this->next();
        }
    }
}
