<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

use Arokettu\Clock\SystemClock;
use DateTimeImmutable;
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

    private static function getTime(
        ClockInterface|DateTimeInterface|float|int|null $clockOrTimestamp,
    ): DateTimeInterface {
        return match (true) {
            $clockOrTimestamp === null
                => self::clock()->now(),
            $clockOrTimestamp instanceof DateTimeInterface
                => $clockOrTimestamp,
            $clockOrTimestamp instanceof ClockInterface
                => $clockOrTimestamp->now(),
            \is_int($clockOrTimestamp)
                => new DateTimeImmutable('@' . $clockOrTimestamp),
            \is_float($clockOrTimestamp)
                => new DateTimeImmutable('@' . sprintf('%.6F', $clockOrTimestamp)),
            default
                => throw new \LogicException('Unhandled type: ' . get_debug_type($clockOrTimestamp)),
        };
    }
}
