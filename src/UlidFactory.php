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

    public static function ulid(
        bool $uuidV7Compatible = false,
        ?ClockInterface $clock = null,
        ?Randomizer $randomizer = null,
    ): Ulid {
        $clock ??= self::clock();
        $randomizer ??= self::randomizer();

        $ts = Helpers\DateTime::buildUlidHex($clock->now());
        $rnd = bin2hex($randomizer->getBytes(10));
        $hex = $ts . $rnd;

        if ($uuidV7Compatible) {
            Helpers\UuidBytes::setVariant($hex, Helpers\UuidVariant::v10xx);
            Helpers\UuidBytes::setVersion($hex, 7);
        }

        return new Ulid($hex);
    }
}
