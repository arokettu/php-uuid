<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

use LogicException;

/**
 * @internal
 */
final class UuidBytes
{
    public static function getVariant(string $hex): ?UuidVariant
    {
        // $hex[16] >> 2 === 0b10
        if ($hex[16] === '8' || $hex[16] === '9' || $hex[16] === 'a' || $hex[16] === 'b') {
            return UuidVariant::v10xx; // RFC 4122
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

    public static function setVariant(string &$hex, UuidVariant $variant): void
    {
        if ($variant !== UuidVariant::v10xx) {
            // @codeCoverageIgnoreStart
            throw new LogicException('Only variant 10xx is supported');
            // @codeCoverageIgnoreEnd
        }

        // $hex[16] & 0b00_11 | 0b10_00
        // 10 times faster than actually doing math
        $hex[16] = match ($hex[16]) {
            '0', '4', '8', 'c' => '8',
            '1', '5', '9', 'd' => '9',
            '2', '6', 'a', 'e' => 'a',
            '3', '7', 'b', 'f' => 'b',
        };
    }

    public static function setVersion(string &$hex, int $version): void
    {
        // check for valid version numbers
        if ($version < 1 || $version > 8) {
            // @codeCoverageIgnoreStart
            throw new LogicException('Only versions 1-8 are supported');
            // @codeCoverageIgnoreEnd
        }

        $hex[12] = \strval($version);
    }
}
