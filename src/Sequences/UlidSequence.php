<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Sequences;

use Arokettu\Clock\SystemClock;
use Arokettu\DateTime\DateTimeTruncate;
use Arokettu\Uuid\Helpers;
use Arokettu\Uuid\Ulid;
use DateInterval;
use DateTimeImmutable;
use Generator;
use Psr\Clock\ClockInterface;
use Random\Engine\Secure;
use Random\Randomizer;

/**
 * @implements UuidSequence<Ulid>
 */
final class UlidSequence implements UuidSequence
{
    private const MAX_COUNTER = 0x0fff_ffff; // 28 last bits to avoid signed int on 32-bit systems
    private const MAX_INCREMENT = 0xffff; // increment with 2 bytes of randomness

    private static DateInterval $ONE_MS;

    private DateTimeImmutable $time;
    private string $hex;
    private int $counter;

    public function __construct(
        private readonly bool $uuidV7Compatible = false,
        private readonly ClockInterface $clock = new SystemClock(),
        private readonly Randomizer $randomizer = new Randomizer(new Secure()),
    ) {
        // init 'const' if not initialized
        self::$ONE_MS ??= DateInterval::createFromDateString('1ms');
    }

    public function next(): Ulid
    {
        $time = DateTimeTruncate::toMilliseconds($this->clock->now()); // we need to round to correctly compare datetime

        if (!isset($this->time) || $this->time < $time) {
            $this->time = $time;

            // a slightly nonstandard layout is used for the ULID here:
            // 48 bit timestamp + 56 bit "constant" random sequence + 24 bit random initialized counter

            $this->hex = $this->generateHex();
            $this->counter = $this->randomizer->getInt(0, self::MAX_COUNTER);
        } else {
            $this->counter += $this->randomizer->getInt(0, self::MAX_INCREMENT);
            if ($this->counter > self::MAX_COUNTER) {
                // do not allow counter rollover

                $this->time = $this->time->add(self::$ONE_MS);
                $this->hex = $this->generateHex();
                $this->counter = $this->randomizer->getInt(0, self::MAX_COUNTER);
            }
        }

        $hex =
            Helpers\DateTime::buildUlidHex($this->time) .
            $this->hex .
            str_pad(dechex($this->counter), 7, '0', STR_PAD_LEFT);

        return new Ulid($hex);
    }

    private function generateHex(): string
    {
        $hex = bin2hex($this->randomizer->getBytes(7));

        if ($this->uuidV7Compatible) {
            // Version 7: set the highest 4 bits to hex '7'
            $hex[0] = '7';
            // Variant 1: set the highest 2 bits to bin 10
            $hex[4] = dechex(0b1000 | hexdec($hex[4]) & 0b0011);
        }

        return substr($hex, 0, 13);
    }

    public function getIterator(): Generator
    {
        while (true) {
            yield $this->next();
        }
    }
}
