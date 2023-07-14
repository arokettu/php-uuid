<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

interface Rfc4122Variant10xxUuid extends Rfc4122Uuid
{
    public function getRfc4122Version(): int;
}
