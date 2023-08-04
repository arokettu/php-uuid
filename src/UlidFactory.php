<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use Arokettu\Clock\SystemClock;
use Psr\Clock\ClockInterface;
use Random\Randomizer;

/**
 * @psalm-api
 */
final class UlidFactory
{
    use Helpers\CachedFactoryObjects;

    public static function sequence(
        bool $uuidV7Compatible = false,
        bool $reserveHighestCounterBit = true,
        ?ClockInterface $clock = null,
        ?Randomizer $randomizer = null,
    ): UlidMonotonicSequence {
        return new UlidMonotonicSequence(
            $uuidV7Compatible,
            $reserveHighestCounterBit,
            $clock ?? self::clock(),
            $randomizer ?? self::rnd(),
        );
    }

    public static function ulid(
        bool $uuidV7Compatible = false,
        ?ClockInterface $clock = null,
        ?Randomizer $randomizer = null,
    ): Ulid {
        return self::sequence($uuidV7Compatible, false, $clock, $randomizer)->next();
    }
}
