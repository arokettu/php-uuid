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
            $ts = intdiv($tsMs, 1000);
            $ms = $tsMs % 1000;
            return DateTimeImmutable::createFromFormat('U u', sprintf('%d %03d', $ts, $ms));
        } else {
            throw new \LogicException('not implemented'); // todo
        }
    }
}
