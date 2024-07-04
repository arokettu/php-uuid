<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use DateTimeInterface;
use Psr\Clock\ClockInterface;
use Random\Randomizer;

/**
 * @psalm-api
 */
final class UlidFactory
{
    use Helpers\FactoryClock;
    use Helpers\FactoryRandomizer;

    public static function ulid(
        bool $uuidV7Compatible = false,
        DateTimeInterface|ClockInterface|null $time = null,
        Randomizer|null $randomizer = null,
    ): Ulid {
        $randomizer ??= self::randomizer();

        $ts = Helpers\DateTime::buildUlidHex(self::getTime($time));
        $rnd = bin2hex($randomizer->getBytes(10));
        $hex = $ts . $rnd;

        if ($uuidV7Compatible) {
            Helpers\UuidBytes::setVariant($hex, Helpers\UuidVariant::v10xx);
            Helpers\UuidBytes::setVersion($hex, 7);
        }

        return new Ulid($hex);
    }
}
