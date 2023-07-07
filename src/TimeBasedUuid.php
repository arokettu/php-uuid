<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use DateTimeImmutable;

interface TimeBasedUuid extends Uuid
{
    /**
     * @psalm-api
     */
    public function getDateTime(): DateTimeImmutable;
}
