<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use Psr\Clock\ClockInterface;
use Random\Randomizer;

final class SequenceFactory
{
    use Helpers\FactoryClock;
    use Helpers\FactoryRandomizer;

    public static function v1(
        ?Nodes\Node $node = null,
        ?ClockInterface $clock = null,
        ?Randomizer $randomizer = null,
    ): Sequences\UuidV1Sequence {
        $randomizer ??= self::randomizer();

        return new Sequences\UuidV1Sequence(
            $node ?? Nodes\StaticNode::random($randomizer),
            $clock ?? self::clock(),
            $randomizer,
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
        $randomizer ??= self::randomizer();

        return new Sequences\UuidV6Sequence(
            $node ?? Nodes\StaticNode::random($randomizer),
            $clock ?? self::clock(),
            $randomizer,
        );
    }

    public static function v7(
        ?ClockInterface $clock = null,
        ?Randomizer $randomizer = null,
    ): Sequences\UuidV7ShortSequence {
        return self::v7Short($clock, $randomizer);
    }

    public static function v7Short(
        ?ClockInterface $clock = null,
        ?Randomizer $randomizer = null,
    ): Sequences\UuidV7ShortSequence {
        return new Sequences\UuidV7ShortSequence(
            $clock ?? self::clock(),
            $randomizer ?? self::randomizer(),
        );
    }

    public static function v7Long(
        ?ClockInterface $clock = null,
        ?Randomizer $randomizer = null,
    ): Sequences\UuidV7LongSequence {
        return new Sequences\UuidV7LongSequence(
            $clock ?? self::clock(),
            $randomizer ?? self::randomizer(),
        );
    }

    public static function ulid(
        bool $uuidV7Compatible = false,
        ?ClockInterface $clock = null,
        ?Randomizer $randomizer = null,
    ): Sequences\UlidSequence {
        return new Sequences\UlidSequence(
            $uuidV7Compatible,
            $clock ?? self::clock(),
            $randomizer ?? self::randomizer(),
        );
    }
}
