<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

use Arokettu\Clock\SystemClock;
use Random\Engine\Secure;
use Random\Randomizer;

trait CachedFactoryObjects
{
    private static SystemClock $clock;
    private static Randomizer $randomizer;

    private static function clock(): SystemClock
    {
        return self::$clock ??= new SystemClock();
    }

    private static function rnd(): Randomizer
    {
        return self::$randomizer ??= new Randomizer(new Secure());
    }
}
