<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use DateTimeImmutable;

final readonly class UuidV2 extends AbstractUuid implements Rfc4122Variant10xxUuid, TimeBasedUuid
{
    use Helpers\Rfc4122Variant1UUID;

    public function getRfc4122Version(): int
    {
        return 2;
    }

    public function getDateTime(): DateTimeImmutable
    {
        $timeMid = substr($this->hex, 8, 4); // 2 bytes after 32 bit identifier
        $timeHigh = substr($this->hex, 13, 3); // next 2 bytes, skip version

        return Helpers\DateTime::parseUuidV1Hex($timeHigh . $timeMid . '00000000');
    }
}
