<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

use DateTimeImmutable;

/**
 * @internal
 */
trait UlidLikeDateTime
{
    protected readonly string $bytes;

    public function getDateTime(): DateTimeImmutable
    {
        $tsBytes = substr($this->bytes, 0, 6); // first 48 bits are a timestamp

        if (PHP_INT_SIZE >= 8) { // 64 bit - a simple way
            $tsMs = hexdec(bin2hex($tsBytes));
            $neg = '';
            if ($tsMs & 0x8000_0000_0000) { // highest bit is 1, negative timestamp
                $tsMs ^= 0xffff_ffff_ffff;
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
