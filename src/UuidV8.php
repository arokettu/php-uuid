<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

final readonly class UuidV8 extends Uuid
{
    use Helpers\Rfc4122Variant1UUID;

    private function version(): int
    {
        return 8;
    }
}
