<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

final readonly class UuidV3 extends AbstractUuid implements Variant10xxUuid
{
    use Helpers\Variant10xxUuidTrait;

    public function getVersion(): int
    {
        return 3;
    }
}
