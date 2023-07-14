<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use DateTimeImmutable;

final readonly class UuidV1 extends AbstractUuid implements Rfc4122Variant10xxUuid, TimeBasedUuid
{
    use Helpers\Rfc4122Variant1UUID;

    public function getRfc4122Version(): int
    {
        return 1;
    }

    public function getDateTime(): DateTimeImmutable
    {
        $timeLow = substr($this->hex, 0, 8); // first 4 bytes
        $timeMid = substr($this->hex, 8, 4); // next 2 bytes
        $timeHigh = substr($this->hex, 13, 3); // next 2 bytes, skip version

        return Helpers\DateTime::parseUuidV1Hex($timeHigh . $timeMid . $timeLow);
    }

    /**
     * @psalm-api
     */
    public function toUuidV6(): UuidV6
    {
        // rearrange time fields
        $time =
            substr($this->hex, 13, 3) .
            substr($this->hex, 8, 4) .
            substr($this->hex, 0, 5) .
            '6' . // version
            substr($this->hex, 5, 3);
        $tail = substr($this->hex, 16);

        return new UuidV6($time . $tail);
    }
}
