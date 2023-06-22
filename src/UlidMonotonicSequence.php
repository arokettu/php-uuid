<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use Arokettu\Clock\SystemClock;
use Generator;
use IteratorAggregate;
use Psr\Clock\ClockInterface;
use Random\Randomizer;

use function Arokettu\Unsigned\add_int;

final class UlidMonotonicSequence implements IteratorAggregate
{
    private ?string $lastTimestamp = null;
    private string $lastBytes;

    public function __construct(
        private readonly bool $uuidV7Compatible = false,
        private readonly Randomizer $randomizer = new Randomizer(),
        private readonly ClockInterface $clock = new SystemClock(),
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
            // 10 bytes, don't bother with native
            $bytes = strrev(add_int(strrev($this->lastBytes), 1));

            if (strcmp($bytes, $this->lastBytes) <= 0) {
                // do not allow counter rollover
                throw new \RuntimeException('Random component overflow');
            }

            $this->lastBytes = $bytes;
        } else {
            $bytes = $this->randomizer->getBytes(10);

            if ($this->uuidV7Compatible) {
                // set variant
                $bytes[2] = \chr(0b10 << 6 | \ord($bytes[8]) & 0b111111); // Variant 1: set the highest 2 bits to bin 10
                // set version
                $bytes[0] = \chr(0x7 << 4 | \ord($bytes[6]) & 0b1111); // Version 8: set the highest 4 bits to hex '8'
            }

            $this->lastBytes = $bytes;
            $this->lastTimestamp = $bytesTS;
        }

        $bytes = $bytesTS . $this->lastBytes;

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
