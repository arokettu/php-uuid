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
        $bytes = md5($namespace->toHex() . $identifier, true);

        // set variant
        $bytes[8] = \chr(0b10 << 6 | \ord($bytes[8]) & 0b111111); // Variant 1: set the highest 2 bits to bin 10
        // set version
        $bytes[6] = \chr(0x3 << 4 | \ord($bytes[6]) & 0b1111); // Version 3: set the highest 4 bits to hex '3'

        return new UuidV3($bytes);
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
        $bytes = substr(sha1($namespace->toHex() . $identifier, true), 0, 16);

        // set variant
        $bytes[8] = \chr(0b10 << 6 | \ord($bytes[8]) & 0b111111); // Variant 1: set the highest 2 bits to bin 10
        // set version
        $bytes[6] = \chr(0x5 << 4 | \ord($bytes[6]) & 0b1111); // Version 5: set the highest 4 bits to hex '5'

        return new UuidV5($bytes);
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
            throw new \ValueError('$bytes must be 16 bytes long');
        }

        // set variant
        $bytes[8] = \chr(0b10 << 6 | \ord($bytes[8]) & 0b111111); // Variant 1: set the highest 2 bits to bin 10
        // set version
        $bytes[6] = \chr(0x8 << 4 | \ord($bytes[6]) & 0b1111); // Version 8: set the highest 4 bits to hex '8'

        return new UuidV8($bytes);
    }

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
