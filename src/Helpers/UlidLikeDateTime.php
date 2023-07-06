<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

use DateTimeImmutable;

/**
 * @internal
 */
trait UlidLikeDateTime
{
    protected readonly string $hex;

    public function getDateTime(): DateTimeImmutable
    {
        $tsHex = substr($this->hex, 0, 12); // first 48 bits are a timestamp

        if (PHP_INT_SIZE >= 8) { // 64 bit - a simple way
            $tsMs = hexdec($tsHex);
            $neg = '';
            if ($tsMs & 0x8000_0000_0000) { // highest bit is 1, negative timestamp
                $tsMs = $tsMs ^ 0xffff_ffff_ffff - 1;
                $neg = '-';
            }
            $ts = intdiv($tsMs, 1000);
            $ms = $tsMs % 1000;
            return new DateTimeImmutable(sprintf('@%s%d.%03d', $neg, $ts, $ms));
        } else {
            throw new \LogicException('not implemented'); // todo
        }
    }
}
