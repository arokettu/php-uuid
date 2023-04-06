<?php

namespace Arokettu\Uuid\Helpers;

/**
 * @internal
 */
final class Base32
{
    public static function encode(string $bytes): string
    {
        return
            self::encode5bytes(substr($bytes, 0, 1))[0] .
            str_pad(self::encode5bytes(substr($bytes, 1, 5)), 8, '0') .
            str_pad(self::encode5bytes(substr($bytes, 6, 5)), 8, '0') .
            str_pad(self::encode5bytes(substr($bytes, 11, 5)), 8, '0');
    }

    private static function encode5bytes(string $bytes): string
    {
        if (PHP_INT_SIZE >= 8) {
            $num = base_convert(bin2hex($bytes), 16, 32);
            return strtr(
                $num,
                '0123456789abcdefghijklmnopqrstuv',
                '0123456789ABCDEFGHJKMNPQRSTVWXYZ',
            );
        } else {
            throw new \LogicException('Not implemented');
        }
    }
}
