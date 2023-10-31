<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use Arokettu\Clock\SystemClock;
use Generator;
use IteratorAggregate;
use Psr\Clock\ClockInterface;
use Random\Engine\Secure;
use Random\Randomizer;

/**
 * @implements IteratorAggregate<int, UuidV7>
 */
final class UuidV7MonotonicSequence implements IteratorAggregate
{
    private ?string $lastTimestamp = null;
    private int $counter = 0;

    public function __construct(
        private readonly bool $reserveHighestCounterBit = true,
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
            $counter &= $this->reserveHighestCounterBit ? 0x07ff : 0x0fff;

            $this->counter = $counter;
            $this->lastTimestamp = $hexTS;
        }

        // attach version 7 to the counter directly
        $hex = $hexTS . dechex($this->counter | 0x7000) . bin2hex($this->randomizer->getBytes(8));

        // set variant
        Helpers\UuidBytes::setVariant($hex, Helpers\UuidVariant::RFC4122);

        return new UuidV7($hex);
    }

    /**
     * @return \Traversable<int, UuidV7>
     */
    public function getIterator(): Generator
    {
        while (true) {
            yield $this->next();
        }
    }
}
