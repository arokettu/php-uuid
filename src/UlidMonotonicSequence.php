<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use Arokettu\Clock\SystemClock;
use Generator;
use IteratorAggregate;
use Psr\Clock\ClockInterface;
use Random\Randomizer;

final class UlidMonotonicSequence implements IteratorAggregate
{
    private ?string $lastTimestamp = null;
    private string $lastBytes;
    private int $counter;

    public function __construct(
        private readonly bool $uuidV7Compatible = false,
        private readonly bool $reserveHighestCounterBit = true,
        private readonly ClockInterface $clock = new SystemClock(),
        private readonly Randomizer $randomizer = new Randomizer(),
    ) {
    }

    public function next(): Ulid
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
            $this->counter++;
            if ($this->counter > 0xff_ff_ff) {
                // do not allow counter rollover
                throw new \RuntimeException('Counter sequence overflow');
            }
        } else {
            $bytes = $this->randomizer->getBytes(10);

            if ($this->uuidV7Compatible) {
                // set variant
                $bytes[2] = \chr(0b10 << 6 | \ord($bytes[2]) & 0b111111); // Variant 1: set the highest 2 bits to bin 10
                // set version
                $bytes[0] = \chr(0x7 << 4 | \ord($bytes[0]) & 0b1111); // Version 7: set the highest 4 bits to hex '7'
            }

            $counter = hexdec(bin2hex(substr($bytes, -3)));
            if ($this->reserveHighestCounterBit) {
                $counter &= 0x7f_ff_ff;
            }

            $this->lastBytes = substr($bytes, 0, -3);
            $this->counter = $counter;
            $this->lastTimestamp = $bytesTS;
        }

        $bytes = $bytesTS . $this->lastBytes . hex2bin(str_pad(dechex($this->counter), 6, '0'));

        return new Ulid($bytes);
    }

    /**
     * @return Generator<void, null, Ulid, null>
     */
    public function getIterator(): Generator
    {
        while (true) {
            yield $this->next();
        }
    }
}
