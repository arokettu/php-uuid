<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

final class UuidV8 extends BaseUuid
{
    use Helpers\Variant1VersionBasedUUID;

    private function version(): int
    {
        return 8;
    }
}
