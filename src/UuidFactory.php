<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use DomainException;
use Psr\Clock\ClockInterface;
use Random\Randomizer;

/**
 * @psalm-api
 */
final class UuidFactory
{
    use Helpers\FactoryClock;
    use Helpers\FactoryRandomizer;

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
        $clock ??= self::clock();
        $randomizer ??= self::randomizer();
        $node ??= new Nodes\RandomNode($randomizer); // override randomizer in the node too

        $tsHex = Helpers\DateTime::buildUuidV1Hex($clock->now());
        $nodeHex = $node->getHex();
        $clockSequenceHex = bin2hex($randomizer->getBytes(2));

        $hex =
            substr($tsHex, 7, 8) . // time_low
            substr($tsHex, 3, 4) . // time_mid
            '1' . // version
            substr($tsHex, 0, 3) . // time_high
            $clockSequenceHex .
            $nodeHex;

        Helpers\UuidBytes::setVariant($hex, Helpers\UuidVariant::v10xx);

        return new UuidV1($hex);
    }

    public static function v2(
        int $domain,
        int $identifier,
        ?Nodes\Node $node = null,
        ?ClockInterface $clock = null,
        ?Randomizer $randomizer = null,
    ): UuidV2 {
        if ($domain < 0 || $domain > 0xff) {
            throw new DomainException('Domain must be in range 0-255');
        }
        if ($identifier < 0 || $identifier >= 2 ** 32) {
            throw new DomainException("Identifier must be in range 0-4'294'967'295");
        }

        $clock ??= self::clock();
        $randomizer ??= self::randomizer();
        $node ??= new Nodes\RandomNode($randomizer); // override randomizer in the node too

        $tsHex = Helpers\DateTime::buildUuidV1Hex($clock->now());
        $nodeHex = $node->getHex();
        $clockSequenceHex = bin2hex($randomizer->getBytes(1));
        $domainHex = sprintf('%02x', $domain);
        $identifierHex = sprintf('%08x', $identifier);

        $hex =
            $identifierHex .
            substr($tsHex, 3, 4) . // time_mid
            '2' . // version
            substr($tsHex, 0, 3) . // time_high
            $clockSequenceHex .
            $domainHex .
            $nodeHex;

        Helpers\UuidBytes::setVariant($hex, Helpers\UuidVariant::v10xx);

        return new UuidV2($hex);
    }

    public static function v3(Uuid|Namespaces\UuidNamespace $namespace, string $identifier): UuidV3
    {
        $bytes = $namespace instanceof Uuid ? $namespace->toBytes() : $namespace->getBytes();

        $hex = md5($bytes . $identifier);

        Helpers\UuidBytes::setVariant($hex, Helpers\UuidVariant::v10xx);
        Helpers\UuidBytes::setVersion($hex, 3);

        return new UuidV3($hex);
    }

    public static function v4(?Randomizer $randomizer = null): UuidV4
    {
        $randomizer ??= self::randomizer();

        $hex = bin2hex($randomizer->getBytes(16));

        Helpers\UuidBytes::setVariant($hex, Helpers\UuidVariant::v10xx);
        Helpers\UuidBytes::setVersion($hex, 4);

        return new UuidV4($hex);
    }

    public static function v5(Uuid|Namespaces\UuidNamespace $namespace, string $identifier): UuidV5
    {
        $bytes = $namespace instanceof Uuid ? $namespace->toBytes() : $namespace->getBytes();

        $hex = substr(sha1($bytes . $identifier), 0, 32);

        Helpers\UuidBytes::setVariant($hex, Helpers\UuidVariant::v10xx);
        Helpers\UuidBytes::setVersion($hex, 5);

        return new UuidV5($hex);
    }

    public static function v6(
        ?Nodes\Node $node = null,
        ?ClockInterface $clock = null,
        ?Randomizer $randomizer = null,
    ): UuidV6 {
        $clock ??= self::clock();
        $randomizer ??= self::randomizer();
        $node ??= new Nodes\RandomNode($randomizer); // override randomizer in the node too

        $tsHex = Helpers\DateTime::buildUuidV1Hex($clock->now());
        $nodeHex = $node->getHex();
        $clockSequenceHex = bin2hex($randomizer->getBytes(2));

        $hex =
            substr($tsHex, 0, 12) . // time_high + time_mid
            '6' . // version
            substr($tsHex, 12, 3) . // time_low
            $clockSequenceHex .
            $nodeHex;

        Helpers\UuidBytes::setVariant($hex, Helpers\UuidVariant::v10xx);

        return new UuidV6($hex);
    }

    public static function v7(
        ?ClockInterface $clock = null,
        ?Randomizer $randomizer = null,
    ): UuidV7 {
        $clock ??= self::clock();
        $randomizer ??= self::randomizer();

        $ts = Helpers\DateTime::buildUlidHex($clock->now());
        $rnd = bin2hex($randomizer->getBytes(10));
        $hex = $ts . $rnd;

        Helpers\UuidBytes::setVariant($hex, Helpers\UuidVariant::v10xx);
        Helpers\UuidBytes::setVersion($hex, 7);

        return new UuidV7($hex);
    }

    public static function v8(string $bytes): UuidV8
    {
        if (\strlen($bytes) !== 16) {
            throw new DomainException('$bytes must be 16 bytes long');
        }

        $hex = bin2hex($bytes);

        Helpers\UuidBytes::setVariant($hex, Helpers\UuidVariant::v10xx);
        Helpers\UuidBytes::setVersion($hex, 8);

        return new UuidV8($hex);
    }
}
