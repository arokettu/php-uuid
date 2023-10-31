<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Sequences;

use Arokettu\Clock\SystemClock;
use Arokettu\Uuid\Helpers;
use Arokettu\Uuid\UuidV7;
use Generator;
use IteratorAggregate;
use Psr\Clock\ClockInterface;
use Random\Engine\Secure;
use Random\Randomizer;

/**
 * @implements UuidSequence<UuidV7>
 */
final class UuidV7Sequence implements IteratorAggregate
{
    private ?string $lastTimestamp = null;
    private int $counter = 0;

    public function __construct(
        private readonly ClockInterface $clock = new SystemClock(),
        private readonly Randomizer $randomizer = new Randomizer(new Secure()),
    ) {
    }

    public function next(): UuidV7
    {
        $hexTS = Helpers\DateTime::buildUlidHex($this->clock->now());

        if ($hexTS === $this->lastTimestamp) {
            $this->counter += 1;
            if ($this->counter > 0x0fff) {
                // do not allow counter rollover
                throw new \RuntimeException("Counter sequence overflow");
            }
        } else {
            $counter = hexdec(bin2hex($this->randomizer->getBytes(2)));
            $counter &= 0x07ff;

            $this->counter = $counter;
            $this->lastTimestamp = $hexTS;
        }

        // attach version 7 to the counter directly
        $hex = $hexTS . dechex($this->counter | 0x7000) . bin2hex($this->randomizer->getBytes(8));

        // set variant
        Helpers\UuidBytes::setVariant($hex, Helpers\UuidVariant::RFC4122);

        return new UuidV7($hex);
    }

    public function getIterator(): Generator
    {
        while (true) {
            yield $this->next();
        }
    }
}
