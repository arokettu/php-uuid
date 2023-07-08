<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

interface Rfc4122Variant1Uuid extends Rfc4122Uuid
{
    public function getRfc4122Version(): int;
}
