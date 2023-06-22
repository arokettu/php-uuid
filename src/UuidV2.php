<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use DateTimeImmutable;

class UuidV2 extends Uuid implements TimeBasedUuid
{
    use Helpers\Variant1VersionBasedUUID;

    private function version(): int
    {
        return 2;
    }

    public function getDateTime(): DateTimeImmutable
    {
        if (PHP_INT_SIZE >= 8) { // 64 bit - a simple way
            $hex = bin2hex($this->bytes);
            $timeMid = hexdec(substr($hex, 8, 4)); // 2 bytes after 32 bit identifier
            $timeHigh = hexdec(substr($hex, 12, 4)) & 0b0000_1111_1111_1111; // next 2 bytes, strip version

            // 100-nanosecond intervals since midnight 15 October 1582 UTC
            $ts = $timeHigh << 48 | $timeMid << 32;
            $tsS = intdiv($ts, 10_000_000) + Helpers\Constants::V1_EPOCH; // convert to unix timestamp
            $tsUs = intdiv($ts % 10_000_000, 10); // lose 1 decimal of precision (much more is lost by v2 anyway)

            return DateTimeImmutable::createFromFormat('U u', sprintf('%d %06d', $tsS, $tsUs));
        } else {
            throw new \LogicException('not implemented'); // todo
        }
    }
}
