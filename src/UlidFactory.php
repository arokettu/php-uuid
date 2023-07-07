<?php

namespace Arokettu\Uuid;

use Arokettu\Clock\SystemClock;
use Psr\Clock\ClockInterface;
use Random\Randomizer;

final class UlidFactory
{
    public static function sequence(
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
        return self::sequence($uuidV7Compatible, false, $clock, $randomizer)->next();
    }
}
