<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

use Arokettu\Clock\SystemClock;
use DateTimeInterface;
use Psr\Clock\ClockInterface;

/**
 * @internal
 */
trait FactoryClock
{
    private static ClockInterface $clock;

    private static function clock(): ClockInterface
    {
        return self::$clock ??= new SystemClock();
    }

    private static function getTime(ClockInterface|DateTimeInterface|null $clockOrTime): DateTimeInterface
    {
        if ($clockOrTime instanceof DateTimeInterface) {
            return $clockOrTime;
        }

        return ($clockOrTime ?? self::clock())->now();
    }
}
