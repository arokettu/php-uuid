<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use Arokettu\Uuid\ClockSequences\ClockSequence;
use Arokettu\Uuid\Nodes\Node;
use Arokettu\Uuid\Nodes\StaticNode;
use Psr\Clock\ClockInterface;
use Random\Randomizer;

final class SequenceFactory
{
    use Helpers\FactoryClock;
    use Helpers\FactoryRandomizer;

    public static function v1(
        Node|null $node = null,
        int|ClockSequence|null $clockSequence = null,
        ClockInterface|null $clock = null,
        Randomizer|null $randomizer = null,
    ): Sequences\UuidV1Sequence {
        $randomizer ??= self::randomizer();

        return new Sequences\UuidV1Sequence(
            $node ?? StaticNode::random($randomizer),
            $clockSequence,
            $clock ?? self::clock(),
            $randomizer,
        );
    }

    public static function v4(
        Randomizer|null $randomizer = null,
    ): Sequences\UuidV4Sequence {
        return new Sequences\UuidV4Sequence(
            $randomizer ?? self::randomizer(),
        );
    }

    public static function v6(
        Node|null $node = null,
        int|ClockSequence|null $clockSequence = null,
        ClockInterface|null $clock = null,
        Randomizer|null $randomizer = null,
    ): Sequences\UuidV6Sequence {
        $randomizer ??= self::randomizer();

        return new Sequences\UuidV6Sequence(
            $node ?? StaticNode::random($randomizer),
            $clockSequence,
            $clock ?? self::clock(),
            $randomizer,
        );
    }

    public static function v7(
        ClockInterface|null $clock = null,
        Randomizer|null $randomizer = null,
    ): Sequences\UuidV7ShortSequence {
        return self::v7Short($clock, $randomizer);
    }

    public static function v7Short(
        ClockInterface|null $clock = null,
        Randomizer|null $randomizer = null,
    ): Sequences\UuidV7ShortSequence {
        return new Sequences\UuidV7ShortSequence(
            $clock ?? self::clock(),
            $randomizer ?? self::randomizer(),
        );
    }

    public static function v7Long(
        ClockInterface|null $clock = null,
        Randomizer|null $randomizer = null,
    ): Sequences\UuidV7LongSequence {
        return new Sequences\UuidV7LongSequence(
            $clock ?? self::clock(),
            $randomizer ?? self::randomizer(),
        );
    }

    public static function ulid(
        bool $uuidV7Compatible = false,
        ClockInterface|null $clock = null,
        Randomizer|null $randomizer = null,
    ): Sequences\UlidSequence {
        return new Sequences\UlidSequence(
            $uuidV7Compatible,
            $clock ?? self::clock(),
            $randomizer ?? self::randomizer(),
        );
    }
}
