<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

interface Rfc4122Uuid extends Uuid
{
    public function getRfc4122Version(): int;
}
