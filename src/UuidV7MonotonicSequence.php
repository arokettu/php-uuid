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
        private readonly int $counterBits = 4,
        private readonly Randomizer $randomizer = new Randomizer(),
        private readonly ClockInterface $clock = new SystemClock(),
    ) {
        if ($counterBits < 0 || $counterBits > 12) {
            throw new \ValueError('$counterBits must be in range 0-12');
        }
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
            if ($this->counter >= 2 ** $this->counterBits) {
                // do not allow counter rollover
                throw new \RuntimeException(sprintf(
                    "For %d counter bits, batch must be shorter than %d",
                    $this->counterBits,
                    2 ** $this->counterBits,
                ));
            }
        } else {
            $this->counter = 0;
            $this->lastTimestamp = $bytesTS;
        }

        $bytes = $bytesTS . $this->randomizer->getBytes(10);

        // set variant
        $bytes[8] = \chr(0b10 << 6 | \ord($bytes[8]) & 0b111111); // Variant 1: set the highest 2 bits to bin 10

        // set version and counter
        // Version 7: set the highest 4 bits to hex '7'
        // first, optimized versions for specific values
        if ($this->counterBits === 0) {
            $bytes[6] = \chr(0x7 << 4 | \ord($bytes[6]) & 0b1111);
        } if ($this->counterBits === 4) {
            $bytes[6] = \chr(0x7 << 4 | $this->counter);
        } elseif ($this->counterBits < 4) {
            $randomBits = 4 - $this->counterBits;
            $version = 0x7 << 4;
            $counter = $this->counter << $randomBits;
            $random = \ord($bytes[6]) % 2 ** $randomBits;
            $bytes[6] = \chr($version | $counter | $random);
        } else {
            $byte7CounterBits = $this->counterBits - 4;
            $randomBits = 8 - $byte7CounterBits;
            $bytes[6] = \chr(0x7 << 4 | $this->counter >> $byte7CounterBits);
            $counter = $this->counter << $randomBits;
            $random = \ord($bytes[7]) % 2 ** $randomBits;
            $bytes[7] = \chr($counter | $random);
        }

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
