<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use DateTimeInterface;
use Psr\Clock\ClockInterface;
use Random\Randomizer;

// TODO: Rename all $clock to $clockOrTime in 4.0

/**
 * @psalm-api
 */
final class UlidFactory
{
    use Helpers\FactoryClock;
    use Helpers\FactoryRandomizer;

    public static function ulid(
        bool $uuidV7Compatible = false,
        ClockInterface|DateTimeInterface|null $clock = null,
        ?Randomizer $randomizer = null,
    ): Ulid {
        $randomizer ??= self::randomizer();

        $ts = Helpers\DateTime::buildUlidHex(self::getTime($clock));
        $rnd = bin2hex($randomizer->getBytes(10));
        $hex = $ts . $rnd;

        if ($uuidV7Compatible) {
            Helpers\UuidBytes::setVariant($hex, Helpers\UuidVariant::v10xx);
            Helpers\UuidBytes::setVersion($hex, 7);
        }

        return new Ulid($hex);
    }
}
