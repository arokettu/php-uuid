<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

use Random\Engine\Secure;
use Random\Randomizer;

trait CachedRandomizer
{
    private static Randomizer $randomizer;

    private static function rnd(): Randomizer
    {
        return self::$randomizer ??= new Randomizer(new Secure());
    }
}