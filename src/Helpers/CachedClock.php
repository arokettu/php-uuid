<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

use Arokettu\Clock\SystemClock;
use Psr\Clock\ClockInterface;

/**
 * @internal
 */
trait CachedClock
{
    private static ClockInterface $clock;

    private static function clock(): ClockInterface
    {
        return self::$clock ??= new SystemClock();
    }
}
