<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

interface Variant10xxUuid extends Uuid
{
    public function getVersion(): int;
}
