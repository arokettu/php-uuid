<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use DateTimeImmutable;

final class UuidV7 extends Uuid implements TimeBasedUuid
{
    use Helpers\Variant1VersionBasedUUID;
    use Helpers\UlidLikeDateTime;

    private function version(): int
    {
        return 7;
    }
}
