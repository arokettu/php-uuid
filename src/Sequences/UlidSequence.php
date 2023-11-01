<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Sequences;

use Arokettu\Clock\SystemClock;
use Arokettu\Uuid\Helpers;
use Arokettu\Uuid\Ulid;
use Generator;
use IteratorAggregate;
use Psr\Clock\ClockInterface;
use Random\Engine\Secure;
use Random\Randomizer;

/**
 * @implements IteratorAggregate<int, Ulid>
 */
final class UlidSequence implements IteratorAggregate
{
    private ?string $lastTimestamp = null;
    private string $lastHex;
    private int $counter;

    public function __construct(
        private readonly bool $uuidV7Compatible = false,
        private readonly bool $reserveHighestCounterBit = true,
        private readonly ClockInterface $clock = new SystemClock(),
        private readonly Randomizer $randomizer = new Randomizer(new Secure()),
    ) {
    }

    public function next(): Ulid
    {
        $hexTS = Helpers\DateTime::buildUlidHex($this->clock->now());

        if ($hexTS === $this->lastTimestamp) {
            $this->counter++;
            if ($this->counter > 0x00ff_ffff) {
                // do not allow counter rollover
                throw new \RuntimeException('Counter sequence overflow');
            }
        } else {
            $hex = $this->randomizer->getBytes(10);

            if ($this->uuidV7Compatible) {
                // set variant
                $hex[2] = \chr(0b10 << 6 | \ord($hex[2]) & 0b111111); // Variant 1: set the highest 2 bits to bin 10
                // set version
                $hex[0] = \chr(0x7 << 4 | \ord($hex[0]) & 0b1111); // Version 7: set the highest 4 bits to hex '7'
            }

            $counter = hexdec(bin2hex(substr($hex, -3)));
            if ($this->reserveHighestCounterBit) {
                $counter &= 0x007f_ffff;
            }

            $this->lastHex = bin2hex(substr($hex, 0, -3));
            $this->counter = $counter;
            $this->lastTimestamp = $hexTS;
        }

        $hex = $hexTS . $this->lastHex . str_pad(dechex($this->counter), 6, '0');

        return new Ulid($hex);
    }

    /**
     * @return Generator<int, Ulid>
     */
    public function getIterator(): Generator
    {
        while (true) {
            yield $this->next();
        }
    }
}
