<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

use Arokettu\Uuid\Node\RandomNode;
use Random\Randomizer;

trait CachedNode
{
    abstract private static function rnd(): Randomizer;

    private static RandomNode $node;

    private static function node(): RandomNode
    {
        return self::$node ??= new RandomNode(self::rnd());
    }
}
