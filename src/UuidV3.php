<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

final readonly class UuidV3 extends AbstractUuid implements Rfc4122Variant10xxUuid
{
    use Helpers\Rfc4122Variant1UUID;

    public function getRfc4122Version(): int
    {
        return 3;
    }
}
