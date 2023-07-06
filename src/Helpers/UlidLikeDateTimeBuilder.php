<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

/**
 * @internal
 */
final class UlidLikeDateTimeBuilder
{
    public static function buildHex(\DateTimeInterface $dt): string
    {
        $tsS  = $dt->format('U');
        $tsMs = $dt->format('v');

        if (PHP_INT_SIZE >= 8) {
            // 64 bit
            $ts = \intval($tsS) * 1000 + \intval($tsMs);

            // 48 bit (6 byte) timestamp
            $hexTS = dechex($ts);
            if (\strlen($hexTS) < 12) {
                $hexTS = str_pad($hexTS, 12, '0', STR_PAD_LEFT);
            } elseif (\strlen($hexTS) > 12) {
                $hexTS = substr($hexTS, -12); // allow date to roll over on 10889-08-02 lol
            }

            return $hexTS;
        } else {
            throw new \LogicException('32 bit not implemented'); // todo
        }
    }
}
