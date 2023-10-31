<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use Psr\Clock\ClockInterface;
use Random\Randomizer;

final class SequenceFactory
{
    use Helpers\CachedClock;
    use Helpers\CachedRandomizer;

    public static function v1(
        ?Nodes\Node $node = null,
        ?ClockInterface $clock = null,
        ?Randomizer $randomizer = null,
    ): Sequences\UuidV1Sequence {
        return new Sequences\UuidV1Sequence(
            $node ?? Nodes\StaticNode::random(),
            $clock ?? self::clock(),
            $randomizer ?? self::randomizer(),
        );
    }

    public static function v4(
        ?Randomizer $randomizer = null,
    ): Sequences\UuidV4Sequence {
        return new Sequences\UuidV4Sequence(
            $randomizer ?? self::randomizer(),
        );
    }

    public static function v6(
        ?Nodes\Node $node = null,
        ?ClockInterface $clock = null,
        ?Randomizer $randomizer = null,
    ): Sequences\UuidV6Sequence {
        return new Sequences\UuidV6Sequence(
            $node ?? Nodes\StaticNode::random(),
            $clock ?? self::clock(),
            $randomizer ?? self::randomizer(),
        );
    }
}
