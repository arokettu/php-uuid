<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

/**
 * @internal
 */
final class Base32
{
    public static function encode(string $hex): string
    {
        return
            str_pad(self::encode5bytes(substr($hex, 0, 2)), 2, '0', STR_PAD_LEFT) .
            str_pad(self::encode5bytes(substr($hex, 2, 10)), 8, '0', STR_PAD_LEFT) .
            str_pad(self::encode5bytes(substr($hex, 12, 10)), 8, '0', STR_PAD_LEFT) .
            str_pad(self::encode5bytes(substr($hex, 22, 10)), 8, '0', STR_PAD_LEFT);
    }

    private static function encode5bytes(string $hex): string
    {
        if (PHP_INT_SIZE >= 8) {
            $num = base_convert($hex, 16, 32);
            return strtr(
                $num,
                'abcdefghijklmnopqrstuv',
                'ABCDEFGHJKMNPQRSTVWXYZ',
            );
        } else {
            throw new \LogicException('Not implemented');
        }
    }

    public static function decode(string $base32): string
    {
        $base32 = strtoupper($base32);

        return
            str_pad(self::decode5bytes(substr($base32, 0, 2)), 1, "\0", STR_PAD_LEFT) .
            str_pad(self::decode5bytes(substr($base32, 2, 8)), 5, "\0", STR_PAD_LEFT) .
            str_pad(self::decode5bytes(substr($base32, 10, 8)), 5, "\0", STR_PAD_LEFT) .
            str_pad(self::decode5bytes(substr($base32, 18, 8)), 5, "\0", STR_PAD_LEFT);
    }

    private static function decode5bytes(string $base32): string
    {
        if (PHP_INT_SIZE >= 8) {
            $num = strtr(
                $base32,
                // alphabet + alternative representations
                'ABCDEFGHJKMNPQRSTVWXYZ' . 'ILO',
                'abcdefghijklmnopqrstuv' . '110',
            );
            return str_pad(
                base_convert($num, 32, 16),
                \strlen($base32) === 2 ? 2 : 10,
                '0',
                STR_PAD_LEFT
            );
        } else {
            throw new \LogicException('Not implemented');
        }
    }
}
