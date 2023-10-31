<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use Psr\Clock\ClockInterface;
use Random\Randomizer;

/**
 * @psalm-api
 */
final class UlidFactory
{
    use Helpers\CachedClock;
    use Helpers\CachedRandomizer;

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
            $randomizer ?? self::randomizer(),
        );
    }

    public static function ulid(
        bool $uuidV7Compatible = false,
        ?ClockInterface $clock = null,
        ?Randomizer $randomizer = null,
    ): Ulid {
        $ts = Helpers\DateTime::buildUlidHex(($clock ?? self::clock())->now());
        $rnd = bin2hex(($randomizer ?? self::randomizer())->getBytes(10));
        $hex = $ts . $rnd;

        if ($uuidV7Compatible) {
            Helpers\UuidBytes::setVariant($hex, Helpers\UuidVariant::RFC4122);
            Helpers\UuidBytes::setVersion($hex, 7);
        }

        return new Ulid($hex);
    }
}
