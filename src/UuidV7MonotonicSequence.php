<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use Arokettu\Clock\SystemClock;
use Generator;
use IteratorAggregate;
use Psr\Clock\ClockInterface;
use Random\Randomizer;

final class UuidV7MonotonicSequence implements IteratorAggregate
{
    private ?string $lastTimestamp = null;
    private int $counter = 0;

    public function __construct(
        private readonly bool $reserveHighestCounterBit = true,
        private readonly ClockInterface $clock = new SystemClock(),
        private readonly Randomizer $randomizer = new Randomizer(),
    ) {
    }

    public function next(): UuidV7
    {
        $dt   = $this->clock->now();
        $tsS  = $dt->format('U');
        $tsMs = $dt->format('v');

        if (PHP_INT_SIZE >= 8) {
            // 64 bit
            $ts = \intval($tsS) * 1000 + \intval($tsMs);

            // 48 bit (6 byte) timestamp
            $hexTS = dechex($ts);
            if (\strlen($hexTS) < 12) {
                $hexTS = str_pad($hexTS, 12, '0', STR_PAD_LEFT);
            } elseif (\strlen($hexTS) > 12) {
                $hexTS = substr($hexTS, -12); // allow date to roll over on 10889-08-02 lol
            }
        } else {
            throw new \LogicException('32 bit not implemented'); // todo
        }

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
        Helpers\UuidBytes::setVariant($hex, 1);

        return new UuidV7($hex);
    }

    /**
     * @return Generator<void, null, UuidV7, null>
     */
    public function getIterator(): Generator
    {
        while (true) {
            yield $this->next();
        }
    }
}
