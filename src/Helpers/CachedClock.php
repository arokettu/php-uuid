<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

use Arokettu\Clock\SystemClock;

trait CachedClock
{
    private static SystemClock $clock;

    private static function clock(): SystemClock
    {
        return self::$clock ??= new SystemClock();
    }
}
