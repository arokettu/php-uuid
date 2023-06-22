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

        if ($asUlid) {
            return new Ulid($bytes);
        }

        if (Helpers\UuidBytes::getVariant($bytes) === 1) {
            return match (Helpers\UuidBytes::getVersion($bytes)) {
                1 => new UuidV1($bytes),
                2 => new UuidV2($bytes),
                3 => new UuidV3($bytes),
                4 => new UuidV4($bytes),
                5 => new UuidV5($bytes),
                6 => new UuidV6($bytes),
                7 => new UuidV7($bytes),
                8 => new UuidV8($bytes),
                default => new GenericUuid($bytes),
            };
        }

        if ($bytes === NilUuid::BYTES) {
            return new NilUuid();
        }
        if ($bytes === MaxUuid::BYTES) {
            return new MaxUuid();
        }

        return new GenericUuid($bytes);
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

        return self::fromBytes(hex2bin($hex), $asUlid);
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
