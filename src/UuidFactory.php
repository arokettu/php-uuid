<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use Random\Randomizer;

final class UuidFactory
{
    public static function nil(): CustomUuid
    {
        return new CustomUuid("\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
    }

    public static function v4(Randomizer $randomizer): UuidV4
    {
        $bytes = $randomizer->getBytes(16);

        // set variant
        $bytes[8] = chr(0b10 << 6 | ord($bytes[8]) & 0b111111); // Variant 1: set the highest 2 bits to bin 10
        // set version
        $bytes[6] = chr(0x4 << 4 | ord($bytes[6]) & 0b1111); // Version 4: set the highest 4 bits to hex '4'

        return new UuidV4($bytes);
    }
}
