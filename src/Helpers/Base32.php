<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

/**
 * @internal
 */
final class Base32
{
    public static function encode(string $bytes): string
    {
        return
            str_pad(self::encode5bytes(substr($bytes, 0, 1)), 2, '0', STR_PAD_LEFT) .
            str_pad(self::encode5bytes(substr($bytes, 1, 5)), 8, '0', STR_PAD_LEFT) .
            str_pad(self::encode5bytes(substr($bytes, 6, 5)), 8, '0', STR_PAD_LEFT) .
            str_pad(self::encode5bytes(substr($bytes, 11, 5)), 8, '0', STR_PAD_LEFT);
    }

    private static function encode5bytes(string $bytes): string
    {
        if (PHP_INT_SIZE >= 8) {
            $num = base_convert(bin2hex($bytes), 16, 32);
            return strtr(
                $num,
                'abcdefghijklmnopqrstuv',
                'ABCDEFGHJKMNPQRSTVWXYZ',
            );
        } else {
            throw new \LogicException('Not implemented');
        }
    }

    public static function decode(string $bytes): string
    {
        $bytes = strtoupper($bytes);

        return
            str_pad(self::decode5bytes(substr($bytes, 0, 2)), 1, "\0", STR_PAD_LEFT) .
            str_pad(self::decode5bytes(substr($bytes, 2, 8)), 5, "\0", STR_PAD_LEFT) .
            str_pad(self::decode5bytes(substr($bytes, 10, 8)), 5, "\0", STR_PAD_LEFT) .
            str_pad(self::decode5bytes(substr($bytes, 18, 8)), 5, "\0", STR_PAD_LEFT);
    }

    private static function decode5bytes(string $bytes): string
    {
        if (PHP_INT_SIZE >= 8) {
            $num = strtr(
                $bytes,
                // alphabet + alternative representations
                'ABCDEFGHJKMNPQRSTVWXYZ' . 'ILO',
                'abcdefghijklmnopqrstuv' . '110',
            );
            return hex2bin(
                str_pad(
                    base_convert($num, 32, 16),
                    \strlen($bytes) === 2 ? 2 : 10,
                    '0',
                    STR_PAD_LEFT
                )
            );
        } else {
            throw new \LogicException('Not implemented');
        }
    }
}
