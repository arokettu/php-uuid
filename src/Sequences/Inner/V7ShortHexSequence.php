<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Sequences\Inner;

use Arokettu\Clock\SystemClock;
use Arokettu\Uuid\Helpers;
use Arokettu\Uuid\Sequences\UuidSequence;
use Arokettu\Uuid\UuidV7;
use DateInterval;
use Generator;
use Psr\Clock\ClockInterface;
use Random\Engine\PcgOneseq128XslRr64;
use Random\Randomizer;

/**
 * @internal
 */
final class V7ShortHexSequence implements UuidSequence
{
    private const MAX_COUNTER = 0x0fff;
    private const MAX_COUNTER_GEN = 0x07ff; // do not fill the highest bit to allow more ids in the same msec

    private static DateInterval $ONE_MS;

    private string $time;
    private int $counter = 0;

    public function __construct(
        private readonly ClockInterface $clock = new SystemClock(),
        private readonly Randomizer $randomizer = new Randomizer(new PcgOneseq128XslRr64()),
    ) {
        // init 'const' if not initialized
        self::$ONE_MS ??= DateInterval::createFromDateString('1ms');
    }

    public function next(): UuidV7
    {
        $dt = $this->clock->now();
        $time = Helpers\DateTime::buildUlidHex($dt); // we need to round to correctly compare datetime

        if (!isset($this->time) || strcmp($this->time, $time) < 0) {
            $this->counter = $this->randomizer->getInt(0, self::MAX_COUNTER_GEN);
            $this->time = $time;
        } else {
            $this->counter += 1;
            if ($this->counter > self::MAX_COUNTER) {
                // do not allow counter rollover
                $this->counter = $this->randomizer->getInt(0, self::MAX_COUNTER_GEN);
                $this->time = Helpers\DateTime::buildUlidHex($dt->add(self::$ONE_MS));
            }
        }

        // attach version 7 to the counter directly
        $hex =
            $this->time .
            dechex($this->counter | 0x7000) .
            bin2hex($this->randomizer->getBytes(8));

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
