<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

use Arokettu\Unsigned as u;

/**
 * @internal
 */
final class Base32
{
    public static function encode(string $hex): string
    {
        // @codeCoverageIgnoreStart
        // 32 bit stuff is not covered by the coverage build
        if (\extension_loaded('gmp')) {
            $num = gmp_strval(gmp_init($hex, 16), 32);
        } else {
            $num = u\to_base(u\from_hex($hex, 16), 32);
        }
        // @codeCoverageIgnoreEnd

        $digits = strtr(
            $num,
            'abcdefghijklmnopqrstuv',
            'ABCDEFGHJKMNPQRSTVWXYZ',
        );
        return str_pad($digits, 26, '0', STR_PAD_LEFT);
    }

    public static function decode(string $base32): string
    {
        $base32 = strtoupper($base32);

        $num = strtr(
            $base32,
            // alphabet + alternative representations
            'ABCDEFGHJKMNPQRSTVWXYZ' . 'ILO',
            'abcdefghijklmnopqrstuv' . '110',
        );

        // @codeCoverageIgnoreStart
        // 32 bit stuff is not covered by the coverage build
        if (\extension_loaded('gmp')) {
            $hex = gmp_strval(gmp_init($num, 32), 16);
        } else {
            $hex = u\to_hex(u\from_base($num, 32, 16));
        }
        // @codeCoverageIgnoreEnd

        return str_pad($hex, 32, '0', STR_PAD_LEFT);
    }
}
