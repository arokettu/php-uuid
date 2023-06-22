<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

final class UuidV5 extends Uuid
{
    use Helpers\Variant1VersionBasedUUID;

    private function version(): int
    {
        return 5;
    }
}
