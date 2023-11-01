<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Sequences;

use Arokettu\Clock\RoundingClock;
use Arokettu\Clock\SystemClock;
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
    private const MAX_COUNTER = 0xff_ff_ff; // 3 last bytes to avoid signed int on 32-bit systems

    private static DateInterval $ONE_MS;

    private readonly ClockInterface $clock;

    private DateTimeImmutable $time;
    private string $hex;
    private int $counter;

    public function __construct(
        private readonly bool $uuidV7Compatible = false,
        ClockInterface $clock = new SystemClock(),
        private readonly Randomizer $randomizer = new Randomizer(new Secure()),
    ) {
        $this->clock = RoundingClock::toMilliseconds($clock); // we need to round to correctly compare datetime

        // init 'const' if not initialized
        self::$ONE_MS ??= DateInterval::createFromDateString('1ms');
    }

    public function next(): Ulid
    {
        $time = $this->clock->now();

        if (!isset($this->time) || $this->time < $time) {
            $this->time = $time;

            // a slightly nonstandard layout is used for the ULID here:
            // 48 bit timestamp + 56 bit "constant" random sequence + 24 bit random initialized counter

            $this->hex = $this->generateHex();
            $this->counter = $this->randomizer->getInt(0, self::MAX_COUNTER);
        } else {
            $this->counter++;
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
            str_pad(dechex($this->counter), 6, '0', STR_PAD_LEFT);

        return new Ulid($hex);
    }

    private function generateHex(): string
    {
        $hex = bin2hex($this->randomizer->getBytes(7));

        if ($this->uuidV7Compatible) {
            // Version 7: set the highest 4 bits to hex '7'
            $hex[0] = '7';
            // Variant 1: set the highest 2 bits to bin 10
            $hex[3] = dechex(0b1000 | hexdec($hex[3]) & 0b0011);
        }

        return $hex;
    }

    public function getIterator(): Generator
    {
        while (true) {
            yield $this->next();
        }
    }
}
