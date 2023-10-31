<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use Psr\Clock\ClockInterface;
use Random\Randomizer;

/**
 * @psalm-api
 */
final class UuidFactory
{
    use Helpers\CachedClock;
    use Helpers\CachedNode;
    use Helpers\CachedRandomizer;

    public static function nil(): NilUuid
    {
        return new NilUuid();
    }

    public static function max(): MaxUuid
    {
        return new MaxUuid();
    }

    public static function v1(
        ?Nodes\Node $node = null,
        ?ClockInterface $clock = null,
        ?Randomizer $randomizer = null,
    ): UuidV1 {
        $node ??= self::node();
        $clock ??= self::clock();
        $randomizer ??= self::randomizer();

        $tsHex = Helpers\DateTime::buildUuidV1Hex($clock->now());
        $nodeHex = $node->getHex();
        $clockSequenceHex = bin2hex($randomizer->getBytes(2));

        $hex =
            substr($tsHex, 7, 8) . // time_low
            substr($tsHex, 3, 4) . // time_mid
            '0' . // version placeholder
            substr($tsHex, 0, 3) . // time_high
            $clockSequenceHex .
            $nodeHex;

        Helpers\UuidBytes::setVariant($hex, Helpers\UuidVariant::RFC4122);
        Helpers\UuidBytes::setVersion($hex, 1);

        return new UuidV1($hex);
    }

    public static function v3(Uuid $namespace, string $identifier): UuidV3
    {
        $hex = md5($namespace->toBytes() . $identifier);

        Helpers\UuidBytes::setVariant($hex, Helpers\UuidVariant::RFC4122);
        Helpers\UuidBytes::setVersion($hex, 3);

        return new UuidV3($hex);
    }

    public static function v4(?Randomizer $randomizer = null): UuidV4
    {
        $randomizer ??= self::randomizer();

        $hex = bin2hex($randomizer->getBytes(16));

        Helpers\UuidBytes::setVariant($hex, Helpers\UuidVariant::RFC4122);
        Helpers\UuidBytes::setVersion($hex, 4);

        return new UuidV4($hex);
    }

    public static function v5(Uuid $namespace, string $identifier): UuidV5
    {
        $hex = substr(sha1($namespace->toBytes() . $identifier), 0, 32);

        Helpers\UuidBytes::setVariant($hex, Helpers\UuidVariant::RFC4122);
        Helpers\UuidBytes::setVersion($hex, 5);

        return new UuidV5($hex);
    }

    public static function v6(
        ?Nodes\Node $node = null,
        ?ClockInterface $clock = null,
        ?Randomizer $randomizer = null,
    ): UuidV6 {
        $node ??= self::node();
        $clock ??= self::clock();
        $randomizer ??= self::randomizer();

        $tsHex = Helpers\DateTime::buildUuidV1Hex($clock->now());
        $nodeHex = $node->getHex();
        $clockSequenceHex = bin2hex($randomizer->getBytes(2));

        $hex =
            substr($tsHex, 0, 12) . // time_high + time_mid
            '0' . // version placeholder
            substr($tsHex, 12, 3) . // time_low
            $clockSequenceHex .
            $nodeHex;

        Helpers\UuidBytes::setVariant($hex, Helpers\UuidVariant::RFC4122);
        Helpers\UuidBytes::setVersion($hex, 6);

        return new UuidV6($hex);
    }

    public static function v7Sequence(
        bool $reserveHighestCounterBit = true,
        ?ClockInterface $clock = null,
        ?Randomizer $randomizer = null,
    ): UuidV7MonotonicSequence {
        return new UuidV7MonotonicSequence(
            $reserveHighestCounterBit,
            $clock ?? self::clock(),
            $randomizer ?? self::randomizer(),
        );
    }

    public static function v7(
        ?ClockInterface $clock = null,
        ?Randomizer $randomizer = null,
    ): UuidV7 {
        $ts = Helpers\DateTime::buildUlidHex(($clock ?? self::clock())->now());
        $rnd = bin2hex(($randomizer ?? self::randomizer())->getBytes(10));
        $hex = $ts . $rnd;

        Helpers\UuidBytes::setVariant($hex, Helpers\UuidVariant::RFC4122);
        Helpers\UuidBytes::setVersion($hex, 7);

        return new UuidV7($hex);
    }

    public static function v8(string $bytes): UuidV8
    {
        if (\strlen($bytes) !== 16) {
            throw new \UnexpectedValueException('$bytes must be 16 bytes long');
        }

        $hex = bin2hex($bytes);

        Helpers\UuidBytes::setVariant($hex, Helpers\UuidVariant::RFC4122);
        Helpers\UuidBytes::setVersion($hex, 8);

        return new UuidV8($hex);
    }
}
