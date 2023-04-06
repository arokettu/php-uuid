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

    public static function v4(Randomizer $randomizer = new Randomizer()): UuidV4
    {
        $bytes = $randomizer->getBytes(16);

        // set variant
        $bytes[8] = \chr(0b10 << 6 | \ord($bytes[8]) & 0b111111); // Variant 1: set the highest 2 bits to bin 10
        // set version
        $bytes[6] = \chr(0x4 << 4 | \ord($bytes[6]) & 0b1111); // Version 4: set the highest 4 bits to hex '4'

        return new UuidV4($bytes);
    }

    public static function v7sequence(
        int $counterBits = 4,
        Randomizer $randomizer = new Randomizer(),
        ClockInterface $clock = new SystemClock(),
    ): UuidV7MonotonicSequence {
        return new UuidV7MonotonicSequence($counterBits, $randomizer, $clock);
    }

    public static function v7(
        Randomizer $randomizer = new Randomizer(),
        ClockInterface $clock = new SystemClock(),
    ): UuidV7 {
        return self::v7sequence(0, $randomizer, $clock)->next();
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
}
