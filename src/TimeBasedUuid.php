<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use DateTimeImmutable;

interface TimeBasedUuid
{
    public function getDateTime(): DateTimeImmutable;
}
