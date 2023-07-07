<?php

namespace Arokettu\Uuid;

use Arokettu\Clock\SystemClock;
use Psr\Clock\ClockInterface;
use Random\Randomizer;

final class UlidFactory
{
    public static function ulidSequence(
        bool $uuidV7Compatible = false,
        bool $reserveHighestCounterBit = true,
        ClockInterface $clock = new SystemClock(),
        Randomizer $randomizer = new Randomizer(),
    ): UlidMonotonicSequence {
        return new UlidMonotonicSequence($uuidV7Compatible, $reserveHighestCounterBit, $clock, $randomizer);
    }

    public static function ulid(
        bool $uuidV7Compatible = false,
        ClockInterface $clock = new SystemClock(),
        Randomizer $randomizer = new Randomizer(),
    ): Ulid {
        return self::ulidSequence($uuidV7Compatible, false, $clock, $randomizer)->next();
    }
}
