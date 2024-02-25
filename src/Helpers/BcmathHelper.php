<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

/**
 * @internal
 */
final class BcmathHelper
{
    public static function hexToDec(string $hex): string
    {
        $value = '0';
        $mul = '1';
        $len = \strlen($hex);
        $chunkLen = 6;

        for ($pos = 0; $pos < $len; $pos += 6) {
            $offset = $pos + 6;
            if ($offset > $len) {
                $chunkLen = 6 - ($offset - $len);
            }
            $chunk = substr($hex, -$offset, $chunkLen);
            var_dump($chunk);
            $change = base_convert($chunk, 16, 10);
            $value = bcadd($value, bcmul($change, $mul, 0), 0);
            $mul = bcmul($mul, '16777216', 0);
        }

        return $value;
    }

    public static function decToHex(string $dec, int $length): string
    {
        $hex = '';
        $neg = false;
        if ($dec[0] === '-') {
            $neg = true;
            $dec = substr($dec, 1);
            $dec = bcsub($dec, '1');
        }
        while (bccomp($dec, '0', 0) !== 0) {
            $chunk = bcmod($dec, '16777216', 0); // take 3 bytes
            $dec = bcdiv($dec, '16777216', 0);

            $hex = str_pad(base_convert($chunk, 10, 16), 6, '0', STR_PAD_LEFT) . $hex;
        }

        $hex = match (\strlen($hex) <=> $length) {
            0  => $hex,
            -1 => str_pad($hex, $length, '0', STR_PAD_LEFT),
            1  => substr($hex, -$length),
        };

        if ($neg) {
            $hex = bin2hex(~hex2bin($hex));
        }

        return $hex;
    }
}
