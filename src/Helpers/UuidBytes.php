<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

/**
 * @internal
 */
final class UuidBytes
{
    public static function getVariant(string $hex): ?int
    {
        $bits = hexdec($hex[16]) >> 2;

        if ($bits === 0b10) {
            return 1; // RFC 4122
        }

        return null; // non RFC 4122 UUIDs are irrelevant
    }

    public static function getVersion(string $hex): ?int
    {
        $version = \intval($hex[12]); // 1-8 are valid, no a-f digits

        // check for valid version numbers
        if ($version < 1 || $version > 8) {
            return null;
        }

        return $version;
    }

    public static function setVariant(string &$hex, int $variant): void
    {
        if ($variant !== 1) {
            // @codeCoverageIgnoreStart
            throw new \LogicException('Only variant 1 is supported');
            // @codeCoverageIgnoreEnd
        }

        $hexDigit = hexdec($hex[16]);
        $hexDigit = $hexDigit & 0b00_11 | 0b10_00;
        $hex[16] = dechex($hexDigit);
    }

    public static function setVersion(string &$hex, int $version): void
    {
        // check for valid version numbers
        if ($version < 1 || $version > 8) {
            // @codeCoverageIgnoreStart
            throw new \LogicException('Only versions 1-8 are supported');
            // @codeCoverageIgnoreEnd
        }

        $hex[12] = \strval($version);
    }
}
