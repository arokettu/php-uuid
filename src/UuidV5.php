<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

final readonly class UuidV5 extends Uuid implements Rfc4122Uuid
{
    use Helpers\Rfc4122Variant1UUID;

    private function version(): int
    {
        return 5;
    }
}
