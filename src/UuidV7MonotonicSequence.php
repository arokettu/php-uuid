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
        private readonly Randomizer $randomizer = new Randomizer(),
        private readonly ClockInterface $clock = new SystemClock(),
    ) {
    }

    public function next(): UuidV7
    {
        $ts = $this->clock->now()->format('Uv');

        if (PHP_INT_SIZE >= 8) {
            // 64 bit

            // 48 bit (6 byte) timestamp
            $hexTS = dechex(\intval($ts));
            if (\strlen($hexTS) < 12) {
                $hexTS = str_pad($hexTS, 12, '0', STR_PAD_LEFT);
            } elseif (\strlen($hexTS) > 12) {
                $hexTS = substr($hexTS, -12); // allow date to roll over on 10889-08-02 lol
            }
            $bytesTS = hex2bin($hexTS);
        } else {
            throw new \LogicException('32 bit not implemented'); // todo
        }

        if ($bytesTS === $this->lastTimestamp) {
            $this->counter += 1;
            if ($this->counter > 0x0fff) {
                // do not allow counter rollover
                throw new \RuntimeException("Counter sequence overflow");
            }
        } else {
            $counter = hexdec(bin2hex($this->randomizer->getBytes(2)));
            $counter &= $this->reserveHighestCounterBit ? 0x07ff : 0x0fff;

            $this->counter = $counter;
            $this->lastTimestamp = $bytesTS;
        }

        // attach version 7 to the counter directly
        $bytes = $bytesTS . hex2bin(dechex($this->counter | 0x7000)) . $this->randomizer->getBytes(8);

        // set variant
        $bytes[8] = \chr(0b10 << 6 | \ord($bytes[8]) & 0b111111); // Variant 1: set the highest 2 bits to bin 10

        return new UuidV7($bytes);
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
