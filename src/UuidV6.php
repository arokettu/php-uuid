<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use DateTimeImmutable;

final readonly class UuidV6 extends AbstractUuid implements Variant10xxUuid, TimeBasedUuid, Rfc9562Uuid
{
    use Helpers\Variant10xxUuidTrait;

    public function getVersion(): int
    {
        return 6;
    }

    public function getDateTime(): DateTimeImmutable
    {
        $timeHighMid = substr($this->hex, 0, 12); // first 6 bytes
        $timeLow = substr($this->hex, 13, 3); // next 2 bytes, skip version

        return Helpers\DateTime::parseUuidV1Hex($timeHighMid . $timeLow);
    }

    /**
     * @psalm-api
     */
    public function toUuidV1(): UuidV1
    {
        // rearrange time fields
        $time1 = substr($this->hex, 7, 5);
        $time2 = substr($this->hex, 13, 3);
        $time3 = substr($this->hex, 3, 4);
        $time4 = substr($this->hex, 0, 3);
        $tail  = substr($this->hex, 16); // clock + node

        return new UuidV1("{$time1}{$time2}{$time3}1{$time4}{$tail}");
    }
}
