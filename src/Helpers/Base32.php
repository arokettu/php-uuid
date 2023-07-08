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
        if (PHP_INT_SIZE >= 8) {
            $p0 = substr($hex, 0, 2);
            $p1 = substr($hex, 2, 10);
            $p2 = substr($hex, 12, 10);
            $p3 = substr($hex, 22, 10);

            $c0 = base_convert($p0, 16, 32);
            $c1 = base_convert($p1, 16, 32);
            $c2 = base_convert($p2, 16, 32);
            $c3 = base_convert($p3, 16, 32);

            $num = $c0 .
                str_pad($c1, 8, '0', STR_PAD_LEFT) .
                str_pad($c2, 8, '0', STR_PAD_LEFT) .
                str_pad($c3, 8, '0', STR_PAD_LEFT);
        // @codeCoverageIgnoreStart
        // 32 bit stuff is not covered by the coverage build
        } else {
            $p0 = substr($hex, 0, 2);
            $p1 = substr($hex, 2, 5);
            $p2 = substr($hex, 7, 5);
            $p3 = substr($hex, 12, 5);
            $p4 = substr($hex, 17, 5);
            $p5 = substr($hex, 22, 5);
            $p6 = substr($hex, 27, 5);

            $c0 = base_convert($p0, 16, 32);
            $c1 = base_convert($p1, 16, 32);
            $c2 = base_convert($p2, 16, 32);
            $c3 = base_convert($p3, 16, 32);
            $c4 = base_convert($p4, 16, 32);
            $c5 = base_convert($p5, 16, 32);
            $c6 = base_convert($p6, 16, 32);

            $num = $c0 .
                str_pad($c1, 4, '0', STR_PAD_LEFT) .
                str_pad($c2, 4, '0', STR_PAD_LEFT) .
                str_pad($c3, 4, '0', STR_PAD_LEFT) .
                str_pad($c4, 4, '0', STR_PAD_LEFT) .
                str_pad($c5, 4, '0', STR_PAD_LEFT) .
                str_pad($c6, 4, '0', STR_PAD_LEFT);
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
