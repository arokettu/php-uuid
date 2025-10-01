<?php

/**
 * @copyright 2023 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Uuid\Sequences\Inner;

use Arokettu\Uuid\Helpers;
use DateInterval;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;
use Random\Randomizer;

/**
 * @internal
 */
final class V7ShortHexSequence
{
    private const MAX_COUNTER = 0x0fff;
    private const MAX_COUNTER_GEN = 0x07ff; // do not fill the highest bit to allow more ids in the same msec

    private static DateInterval $ONE_MS;

    private string $time;
    private DateTimeImmutable $dt;
    private int $counter = 0;

    public function __construct(
        private readonly ClockInterface $clock,
        private readonly Randomizer $randomizer,
    ) {
        // init 'const' if not initialized
        self::$ONE_MS ??= DateInterval::createFromDateString('1ms');
    }

    public function next(): string
    {
        $dt = $this->clock->now();
        $time = Helpers\DateTime::buildUlidHex($dt); // we need to round to correctly compare datetime

        if (!isset($this->time) || strcmp($this->time, $time) < 0) {
            $this->counter = $this->randomizer->getInt(0, self::MAX_COUNTER_GEN);
            $this->dt = $dt;
            $this->time = $time;
        } else {
            $this->counter += 1;
            if ($this->counter > self::MAX_COUNTER) {
                // do not allow counter rollover
                $this->counter = $this->randomizer->getInt(0, self::MAX_COUNTER_GEN);
                $this->dt = $this->dt->add(self::$ONE_MS);
                $this->time = Helpers\DateTime::buildUlidHex($this->dt);
            }
        }

        // attach version 7 to the counter directly
        $hex =
            $this->time .
            dechex($this->counter | 0x7000) .
            bin2hex($this->randomizer->getBytes(8));

        // set variant
        Helpers\UuidBytes::setVariant($hex, Helpers\UuidVariant::v10xx);

        return $hex;
    }
}
