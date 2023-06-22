<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

final class UuidV5 extends Uuid
{
    use Helpers\Rfc4122Variant1UUID;

    private function version(): int
    {
        return 5;
    }
}
