<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use DateTimeImmutable;

final readonly class UuidV1 extends AbstractUuid implements Rfc4122Uuid, TimeBasedUuid
{
    use Helpers\Rfc4122Variant1UUID;

    public function getRfc4122Version(): int
    {
        return 1;
    }

    public function getDateTime(): DateTimeImmutable
    {
        if (PHP_INT_SIZE >= 8) { // 64 bit - a simple way
            $hex = bin2hex($this->hex);
            $timeLow = hexdec(substr($hex, 0, 8)); // first 4 bytes
            $timeMid = hexdec(substr($hex, 8, 4)); // next 2 bytes
            $timeHigh = hexdec(substr($hex, 12, 4)) & 0b0000_1111_1111_1111; // next 2 bytes, strip version

            // 100-nanosecond intervals since midnight 15 October 1582 UTC
            $ts = $timeHigh << 48 | $timeMid << 32 | $timeLow;
            $tsS = intdiv($ts, 10_000_000) + Helpers\Constants::V1_EPOCH; // convert to unix timestamp
            $tsUs = intdiv($ts % 10_000_000, 10); // lose 1 decimal of precision

            return DateTimeImmutable::createFromFormat('U u', sprintf('%d %06d', $tsS, $tsUs));
        } else {
            throw new \LogicException('not implemented'); // todo
        }
    }

    public function toUuidV6(): UuidV6
    {
        // 32 bit friendly

        $hex = bin2hex($this->hex);

        // rearrange time fields
        $time =
            substr($hex, 13, 3) .
            substr($hex, 8, 4) .
            substr($hex, 0, 5) .
            '6' . // version
            substr($hex, 5, 3);
        $tail = substr($hex, 16);

        return new UuidV6(hex2bin($time . $tail));
    }
}
