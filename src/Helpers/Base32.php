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
        if (PHP_INT_SIZE >= 8) {
            $h0 = substr($hex, 0, 2);
            $h1 = substr($hex, 2, 10);
            $h2 = substr($hex, 12, 10);
            $h3 = substr($hex, 22, 10);

            $b0 = base_convert($h0, 16, 32);
            $b1 = base_convert($h1, 16, 32);
            $b2 = base_convert($h2, 16, 32);
            $b3 = base_convert($h3, 16, 32);

            $num = \sprintf('%02s%08s%08s%08s', $b0, $b1, $b2, $b3);
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

            $num = \sprintf('%02s%04s%04s%04s%04s%04s%04s', $b0, $b1, $b2, $b3, $b4, $b5, $b6);
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

            $hex = \sprintf('%02s%010s%010s%010s', $h0, $h1, $h2, $h3);
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

            $hex = \sprintf('%02s%05s%05s%05s%05s%05s%05s', $h0, $h1, $h2, $h3, $h4, $h5, $h6);
        }
        // @codeCoverageIgnoreEnd

        return $hex;
    }
}
