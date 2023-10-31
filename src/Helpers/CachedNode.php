<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

use Arokettu\Uuid\Nodes\RandomNode;
use Random\Randomizer;

/**
 * @internal
 */
trait CachedNode
{
    abstract private static function randomizer(): Randomizer;

    private static RandomNode $node;

    private static function node(): RandomNode
    {
        return self::$node ??= new RandomNode(self::randomizer());
    }
}
