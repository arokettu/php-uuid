<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

final readonly class UuidV5 extends AbstractUuid implements Rfc4122Variant10xxUuid
{
    use Helpers\Rfc4122Variant10xxUUID;

    public function getRfc4122Version(): int
    {
        return 5;
    }
}
