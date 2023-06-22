<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

final class UuidV3 extends BaseUuid
{
    use Helpers\Variant1VersionBasedUUID;

    private function version(): int
    {
        return 3;
    }
}
