<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

final class UuidParser
{
    public static function fromBytes(string $bytes, bool $asUlid = false): Uuid
    {
        if (\strlen($bytes) !== 16) {
            throw new \UnexpectedValueException('UUID must be 16 bytes long');
        }

        return self::fromHex(bin2hex($bytes), $asUlid);
    }

    public static function fromHex(string $hex, bool $asUlid = false): Uuid
    {
        if (preg_match('/^[0-9a-f]{32}$/', $hex) !== 1) {
            throw new \ValueError('UUID must be 16 hexadecimal digits');
        }

        $hex = strtolower($hex);

        if ($asUlid) {
            return new Ulid($hex);
        }

        if (Helpers\UuidBytes::getVariant($hex) === 1) {
            return match (Helpers\UuidBytes::getVersion($hex)) {
                1 => new UuidV1($hex),
                2 => new UuidV2($hex),
                3 => new UuidV3($hex),
                4 => new UuidV4($hex),
                5 => new UuidV5($hex),
                6 => new UuidV6($hex),
                7 => new UuidV7($hex),
                8 => new UuidV8($hex),
                default => new GenericUuid($hex),
            };
        }

        if ($hex === NilUuid::HEX) {
            return new NilUuid();
        }
        if ($hex === MaxUuid::HEX) {
            return new MaxUuid();
        }

        return new GenericUuid($hex);
    }

    public static function fromRfc4122(string $string, bool $asUlid = false): Uuid
    {
        $match = preg_match(
            '/' .
            '^\{[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}}$' .
            '|' .
            '^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$' .
            '|' .
            '^\{[0-9a-f]{32}}$' .
            '|' .
            '^[0-9a-f]{32}$' .
            '/i',
            $string
        );

        if (!$match) {
            throw new \UnexpectedValueException('Not a valid RFC 4122 UUID');
        }

        $hex = preg_replace('/[{}-]/', '', $string);

        return self::fromHex(strtolower($hex), $asUlid);
    }

    public static function fromBase32(string $string, bool $asUuid = false): Uuid
    {
        $match = preg_match('/^[0-7][0-9A-TV-Z]{25}$/i', $string);

        if (!$match) {
            throw new \UnexpectedValueException('Not a valid Base32 encoded ULID');
        }

        return self::fromBytes(Helpers\Base32::decode($string), !$asUuid);
    }

    public static function fromString(string $string): Uuid
    {
        return match (\strlen($string)) {
            32, 34, 36, 38 => self::fromRfc4122($string),
            26 => self::fromBase32($string),
            default => throw new \UnexpectedValueException('Format not recognized'),
        };
    }
}
