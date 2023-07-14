<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use DateTimeImmutable;

final readonly class UuidV6 extends AbstractUuid implements Rfc4122Variant10xxUuid, TimeBasedUuid
{
    use Helpers\Rfc4122Variant10xxUUID;

    public function getRfc4122Version(): int
    {
        return 6;
    }

    public function getDateTime(): DateTimeImmutable
    {
        $timeHigh = substr($this->hex, 0, 8); // first 4 bytes
        $timeMid = substr($this->hex, 8, 4); // next 2 bytes
        $timeLow = substr($this->hex, 13, 3); // next 2 bytes, skip version

        return Helpers\DateTime::parseUuidV1Hex($timeHigh . $timeMid . $timeLow);
    }

    /**
     * @psalm-api
     */
    public function toUuidV1(): UuidV1
    {
        // 32 bit friendly
        // rearrange time fields
        $time =
            substr($this->hex, 7, 5) .
            substr($this->hex, 13, 3) .
            substr($this->hex, 3, 4) .
            '1' . // version
            substr($this->hex, 0, 3);
        $tail = substr($this->hex, 16);

        return new UuidV1($time . $tail);
    }
}
