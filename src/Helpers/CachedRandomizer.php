<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

use Random\Engine\Secure;
use Random\Randomizer;

/**
 * @internal
 */
trait CachedRandomizer
{
    private static Randomizer $randomizer;

    private static function randomizer(): Randomizer
    {
        return self::$randomizer ??= new Randomizer(new Secure());
    }
}
