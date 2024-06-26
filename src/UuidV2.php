<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use DateTimeImmutable;

final readonly class UuidV2 extends AbstractUuid implements Variant10xxUuid, TimeBasedUuid
{
    use Helpers\Variant10xxUuidTrait;

    public function getVersion(): int
    {
        return 2;
    }

    public function getDomain(): int
    {
        return hexdec(substr($this->hex, 18, 2));
    }

    public function getIdentifier(): int
    {
        return hexdec(substr($this->hex, 0, 8));
    }

    public function getDateTime(): DateTimeImmutable
    {
        $timeMid = substr($this->hex, 8, 4); // 2 bytes after 32 bit identifier
        $timeHigh = substr($this->hex, 13, 3); // next 2 bytes, skip version

        return Helpers\DateTime::parseUuidV1Hex($timeHigh . $timeMid . '00000000');
    }

    public function __debugInfo(): array
    {
        $info = parent::__debugInfo();
        $info['domain'] = $this->getDomain();
        $info['identifier'] = $this->getIdentifier();
        return $info;
    }
}
