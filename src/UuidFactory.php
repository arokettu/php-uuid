<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use Arokettu\Clock\SystemClock;
use Psr\Clock\ClockInterface;
use Random\Randomizer;

final class UuidFactory
{
    public static function nil(): NilUuid
    {
        return new NilUuid();
    }

    public static function max(): MaxUuid
    {
        return new MaxUuid();
    }

    public static function v3(Uuid $namespace, string $identifier): UuidV3
    {
        $hex = md5($namespace->toBytes() . $identifier);

        Helpers\UuidBytes::setVariant($hex, 1);
        Helpers\UuidBytes::setVersion($hex, 3);

        return new UuidV3($hex);
    }

    public static function v4(Randomizer $randomizer = new Randomizer()): UuidV4
    {
        $hex = bin2hex($randomizer->getBytes(16));

        Helpers\UuidBytes::setVariant($hex, 1);
        Helpers\UuidBytes::setVersion($hex, 4);

        return new UuidV4($hex);
    }

    public static function v5(Uuid $namespace, string $identifier): UuidV5
    {
        $hex = substr(sha1($namespace->toBytes() . $identifier), 0, 32);

        Helpers\UuidBytes::setVariant($hex, 1);
        Helpers\UuidBytes::setVersion($hex, 5);

        return new UuidV5($hex);
    }

    public static function v7Sequence(
        bool $reserveHighestCounterBit = true,
        ClockInterface $clock = new SystemClock(),
        Randomizer $randomizer = new Randomizer(),
    ): UuidV7MonotonicSequence {
        return new UuidV7MonotonicSequence($reserveHighestCounterBit, $clock, $randomizer);
    }

    public static function v7(
        ClockInterface $clock = new SystemClock(),
        Randomizer $randomizer = new Randomizer(),
    ): UuidV7 {
        return self::v7Sequence(false, $clock, $randomizer)->next();
    }

    public static function v8(string $bytes): UuidV8
    {
        if (\strlen($bytes) !== 16) {
            throw new \UnexpectedValueException('$bytes must be 16 bytes long');
        }

        $hex = bin2hex($bytes);

        Helpers\UuidBytes::setVariant($hex, 1);
        Helpers\UuidBytes::setVersion($hex, 8);

        return new UuidV8($hex);
    }
}
