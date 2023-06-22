<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

use DateTimeImmutable;

trait UlidLikeDateTime
{
    protected readonly string $bytes;

    public function getDateTime(): DateTimeImmutable
    {
        $tsBytes = substr($this->bytes, 0, 6); // first 48 bits are a timestamp

        if (PHP_INT_SIZE >= 8) { // 64 bit - a simple way
            $tsMs = hexdec(bin2hex($tsBytes));
            $ts = intdiv($tsMs, 1000);
            $ms = $tsMs % 1000;
            return DateTimeImmutable::createFromFormat('U u', sprintf('%d %03d', $ts, $ms));
        } else {
            throw new \LogicException('not implemented'); // todo
        }
    }
}
