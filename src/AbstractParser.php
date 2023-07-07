<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

/**
 * @template T
 */
abstract class AbstractParser
{
    protected const TYPE = '';

    /**
     * @psalm-api
     * @return T
     */
    abstract public static function fromHex(string $hex): Uuid;

    /**
     * @psalm-api
     * @return T
     */
    public static function fromBytes(string $bytes): Uuid
    {
        if (\strlen($bytes) !== 16) {
            throw new \UnexpectedValueException(static::TYPE . ' must be 16 bytes long');
        }

        return static::fromHex(bin2hex($bytes));
    }

    /**
     * @psalm-api
     * @return T
     */
    public static function fromRfc4122(string $string): Uuid
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
            throw new \UnexpectedValueException('Not a valid RFC 4122 UUID notation');
        }

        $hex = preg_replace('/[{}-]/', '', $string);

        return static::fromHex(strtolower($hex));
    }

    /**
     * @psalm-api
     * @return T
     */
    public static function fromBase32(string $string): Uuid
    {
        $match = preg_match('/^[0-7][0-9A-TV-Z]{25}$/i', $string);

        if (!$match) {
            throw new \UnexpectedValueException('Not a valid Base32 encoded ' . static::TYPE);
        }

        return static::fromHex(Helpers\Base32::decode($string));
    }

    /**
     * @psalm-api
     * @return T
     */
    public static function fromString(string $string): Uuid
    {
        return match (\strlen($string)) {
            32, 34, 36, 38 => self::fromRfc4122($string),
            26 => self::fromBase32($string),
            default => throw new \UnexpectedValueException('Format not recognized'),
        };
    }
}
