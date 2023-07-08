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
            $h0 = substr($hex, 0, 2);
            $h1 = substr($hex, 2, 10);
            $h2 = substr($hex, 12, 10);
            $h3 = substr($hex, 22, 10);

            $b0 = base_convert($h0, 16, 32);
            $b1 = base_convert($h1, 16, 32);
            $b2 = base_convert($h2, 16, 32);
            $b3 = base_convert($h3, 16, 32);

            $num =
                str_pad($b0, 2, '0', STR_PAD_LEFT) .
                str_pad($b1, 8, '0', STR_PAD_LEFT) .
                str_pad($b2, 8, '0', STR_PAD_LEFT) .
                str_pad($b3, 8, '0', STR_PAD_LEFT);
        // @codeCoverageIgnoreStart
        // 32 bit stuff is not covered by the coverage build
        } else {
            $h0 = substr($hex, 0, 2);
            $h1 = substr($hex, 2, 5);
            $h2 = substr($hex, 7, 5);
            $h3 = substr($hex, 12, 5);
            $h4 = substr($hex, 17, 5);
            $h5 = substr($hex, 22, 5);
            $h6 = substr($hex, 27, 5);

            $b0 = base_convert($h0, 16, 32);
            $b1 = base_convert($h1, 16, 32);
            $b2 = base_convert($h2, 16, 32);
            $b3 = base_convert($h3, 16, 32);
            $b4 = base_convert($h4, 16, 32);
            $b5 = base_convert($h5, 16, 32);
            $b6 = base_convert($h6, 16, 32);

            $num =
                str_pad($b0, 2, '0', STR_PAD_LEFT) .
                str_pad($b1, 4, '0', STR_PAD_LEFT) .
                str_pad($b2, 4, '0', STR_PAD_LEFT) .
                str_pad($b3, 4, '0', STR_PAD_LEFT) .
                str_pad($b4, 4, '0', STR_PAD_LEFT) .
                str_pad($b5, 4, '0', STR_PAD_LEFT) .
                str_pad($b6, 4, '0', STR_PAD_LEFT);
        }
        // @codeCoverageIgnoreEnd

        return strtr(
            $num,
            'abcdefghijklmnopqrstuv',
            'ABCDEFGHJKMNPQRSTVWXYZ',
        );
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

        if (PHP_INT_SIZE >= 8) {
            $b0 = substr($num, 0, 2);
            $b1 = substr($num, 2, 8);
            $b2 = substr($num, 10, 8);
            $b3 = substr($num, 18, 8);

            $h0 = base_convert($b0, 32, 16);
            $h1 = base_convert($b1, 32, 16);
            $h2 = base_convert($b2, 32, 16);
            $h3 = base_convert($b3, 32, 16);

            $hex =
                str_pad($h0, 2, '0', STR_PAD_LEFT) .
                str_pad($h1, 10, '0', STR_PAD_LEFT) .
                str_pad($h2, 10, '0', STR_PAD_LEFT) .
                str_pad($h3, 10, '0', STR_PAD_LEFT);
        // @codeCoverageIgnoreStart
        // 32 bit stuff is not covered by the coverage build
        } else {
            $b0 = substr($num, 0, 2);
            $b1 = substr($num, 2, 4);
            $b2 = substr($num, 6, 4);
            $b3 = substr($num, 10, 4);
            $b4 = substr($num, 14, 4);
            $b5 = substr($num, 18, 4);
            $b6 = substr($num, 22, 4);

            $h0 = base_convert($b0, 32, 16);
            $h1 = base_convert($b1, 32, 16);
            $h2 = base_convert($b2, 32, 16);
            $h3 = base_convert($b3, 32, 16);
            $h4 = base_convert($b4, 32, 16);
            $h5 = base_convert($b5, 32, 16);
            $h6 = base_convert($b6, 32, 16);

            $hex =
                str_pad($h0, 2, '0', STR_PAD_LEFT) .
                str_pad($h1, 5, '0', STR_PAD_LEFT) .
                str_pad($h2, 5, '0', STR_PAD_LEFT) .
                str_pad($h3, 5, '0', STR_PAD_LEFT) .
                str_pad($h4, 5, '0', STR_PAD_LEFT) .
                str_pad($h5, 5, '0', STR_PAD_LEFT) .
                str_pad($h6, 5, '0', STR_PAD_LEFT);
        }
        // @codeCoverageIgnoreEnd

        return $hex;
    }
}
