<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Clock\StaticClock;
use Arokettu\Uuid\ClockSequences\ClockSequence;
use Arokettu\Uuid\Namespaces\UuidNamespace;
use Arokettu\Uuid\Nodes\RandomNode;
use Arokettu\Uuid\Nodes\StaticNode;
use Arokettu\Uuid\UuidFactory;
use Arokettu\Uuid\UuidParser;
use Arokettu\Uuid\UuidV1;
use Arokettu\Uuid\UuidV6;
use PHPUnit\Framework\TestCase;
use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

class V1Test extends TestCase
{
    public function testTimestamp(): void
    {
        $uuid = UuidNamespace::URL->getUuid();
        $ts = new \DateTime('1998-02-04T22:13:53.151182+0000');
        self::assertEquals($ts, $uuid->getDateTime());

        // check with the same timestamp as UUIDv2 example
        // {00000002-92e8-11ed-8100-3fdb0085247e}
        $uuid = new UuidV1('0000000292e811ed81003fdb0085247e');
        $ts = new \DateTime('2023-01-13T02:14:24.842137+0000');
        self::assertEquals($ts, $uuid->getDateTime());
    }

    public function testNode(): void
    {
        /** @var UuidV1 $uuid */
        $uuid = UuidParser::fromRfc9562('ba472344-3c9b-11ef-9846-752142c7cc70');
        self::assertEquals('75:21:42:c7:cc:70', $uuid->getNode()->toString());

        /** @var UuidV1 $uuid */
        $uuid = UuidParser::fromRfc9562('ba472344-3c9b-11ef-9846-1445FD782ba3');
        self::assertEquals('14:45:fd:78:2b:a3', $uuid->getNode()->toString());
    }

    public function testClockSequence(): void
    {
        /** @var UuidV1 $uuid */
        $uuid = UuidParser::fromRfc9562('ba472344-3c9b-11ef-9846-752142c7cc70');
        self::assertEquals(0x1846, $uuid->getClockSequence());

        /** @var UuidV1 $uuid */
        $uuid = UuidParser::fromRfc9562('ba472344-3c9b-11ef-bfb9-752142c7cc70');
        self::assertEquals(0x3fb9, $uuid->getClockSequence());
    }

    public function testToUuidV6(): void
    {
        $uuid1 = UuidNamespace::URL->getUuid();
        $uuid6 = new UuidV6('1d19dad6ba7b681180b400c04fd430c8');

        self::assertEquals($uuid6->toString(), $uuid1->toUuidV6()->toString());

        // fields do not change
        self::assertEquals($uuid6->getDateTime(), $uuid1->getDateTime());
        self::assertEquals($uuid6->getClockSequence(), $uuid1->getClockSequence());
        self::assertEquals($uuid6->getNode(), $uuid1->getNode());
    }

    public function testFactory(): void
    {
        $clock = new StaticClock(
            new \DateTimeImmutable('2023-10-29 17:04:00.123456 UTC') // 1ee767d27875680
        );
        $rand = new Randomizer(new Xoshiro256StarStar(123)); // f969a0d1a18f5a32
        $node = StaticNode::fromHex('1234567890ab'); // 1334567890ab

        $uuid = UuidFactory::v1($node, ClockSequence::Random, $clock, $rand);
        self::assertEquals('27875680-767d-11ee-a9f9-1334567890ab', $uuid->toString());

        $fixedCS = 0xabcd & 0x3fff;

        // time underflow
        $clock = new StaticClock(new \DateTimeImmutable('1585-01-01 1:02 UTC')); // 0027bbfb17ab400
        $uuid = UuidFactory::v1($node, $fixedCS, $clock);
        self::assertEquals('b17ab400-7bbf-1002-abcd-1334567890ab', $uuid->toString());

        // time overflow
        $clock = new StaticClock(new \DateTimeImmutable('6000-01-01 2:03 UTC')); // [1]358432968ac2200
        $uuid = UuidFactory::v1($node, $fixedCS, $clock);
        self::assertEquals('68ac2200-4329-1358-abcd-1334567890ab', $uuid->toString());
    }

    public function testSystemTime(): void
    {
        $time1 = new \DateTime();
        $uuid = UuidFactory::v1();
        $time2 = new \DateTime();

        self::assertGreaterThanOrEqual($time1, $uuid->getDateTime());
        self::assertLessThanOrEqual($time2, $uuid->getDateTime());
    }

    public function testEquivalentToV6(): void
    {
        // V1 and V6 on the same node at the same time should be the same
        $clock = new StaticClock();
        $randEngine = new Xoshiro256StarStar();
        $randomizer1 = new Randomizer(clone $randEngine);
        $randomizer6 = new Randomizer(clone $randEngine);
        $node = StaticNode::fromHex('1234567890ab');

        $uuid1 = UuidFactory::v1($node, ClockSequence::Random, $clock, $randomizer1);
        $uuid6 = UuidFactory::v6($node, ClockSequence::Random, $clock, $randomizer6);

        self::assertEquals($uuid6->toString(), $uuid1->toUuidV6()->toString());
    }

    public function testRfcExample(): void
    {
        $time = new \DateTime('February 22, 2022 2:22:22.000000PM GMT-05:00');
        $clockSeq = 0b11 << 12 | 0x3c8;
        $node = StaticNode::fromHex('9E6BDECED846');

        $uuid = UuidFactory::v1($node, $clockSeq, $time);
        self::assertEquals('C232AB00-9414-11EC-B3C8-9F6BDECED846', strtoupper($uuid->toString()));
    }

    // if RNG was overridden in a factory, do not use global randomizer for the node
    public function testRandomizerOverridden(): void
    {
        $engine = new Xoshiro256StarStar(); // any seed
        $r1 = new Randomizer(clone $engine);
        $r2 = new Randomizer(clone $engine);

        $clock = new StaticClock();

        $uuid1 = UuidFactory::v1(timestamp: $clock, randomizer: $r1)->toString();
        $uuid2 = UuidFactory::v1(node: new RandomNode($r2), timestamp: $clock, randomizer: $r2)->toString();

        self::assertEquals($uuid1, $uuid2);
    }
}
