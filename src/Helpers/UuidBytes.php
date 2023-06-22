<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

/**
 * @internal
 */
final class UuidBytes
{
    public static function getVariant(string $bytes): ?int
    {
        $bits = \ord($bytes[8]) >> 6;

        if ($bits === 0b10) {
            return 1; // RFC 4122
        }

        return null; // non RFC 4122 UUIDs are irrelevant
    }

    public static function getVersion(string $bytes): ?int
    {
        $bits = \ord($bytes[6]) >> 4;

        // check for valid version numbers
        if ($bits < 1 || $bits > 8) {
            return null;
        }

        return $bits;
    }
}
