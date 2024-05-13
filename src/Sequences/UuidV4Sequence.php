<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Sequences;

use Arokettu\Uuid\UuidFactory;
use Arokettu\Uuid\UuidV4;
use Generator;
use Random\Engine\PcgOneseq128XslRr64;
use Random\Randomizer;

/**
 * @implements UuidSequence<UuidV4>
 */
final readonly class UuidV4Sequence implements UuidSequence
{
    public function __construct(
        private Randomizer $randomizer = new Randomizer(new PcgOneseq128XslRr64()),
    ) {
    }

    public function next(): UuidV4
    {
        return UuidFactory::v4($this->randomizer);
    }

    public function getIterator(): Generator
    {
        while (true) {
            yield $this->next();
        }
    }
}
