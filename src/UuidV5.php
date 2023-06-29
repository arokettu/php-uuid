<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

final readonly class UuidV5 extends AbstractUuid implements Rfc4122Uuid
{
    use Helpers\Rfc4122Variant1UUID;

    public function getRfc4122Version(): int
    {
        return 5;
    }
}
