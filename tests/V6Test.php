<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Clock\StaticClock;
use Arokettu\Uuid\Namespaces\UuidNamespace;
use Arokettu\Uuid\Nodes\RandomNode;
use Arokettu\Uuid\Nodes\StaticNode;
use Arokettu\Uuid\UuidFactory;
use Arokettu\Uuid\UuidParser;
use Arokettu\Uuid\UuidV6;
use PHPUnit\Framework\TestCase;
use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

final class V6Test extends TestCase
{
    public function testTimestamp(): void
    {
        // {1d19dad6-ba7b-6811-80b4-00c04fd430c8}
        $uuid = new UuidV6('1d19dad6ba7b681180b400c04fd430c8');
        $ts = new \DateTime('1998-02-04T22:13:53.151182+0000');
        self::assertEquals($ts, $uuid->getDateTime());
    }

    public function testNode(): void
    {
        /** @var UuidV6 $uuid */
        $uuid = UuidParser::fromRfc9562('1d19dad6-ba7b-6811-80b4-752142c7cc70');
        self::assertEquals('75:21:42:c7:cc:70', $uuid->getNode()->toString());

        /** @var UuidV6 $uuid */
        $uuid = UuidParser::fromRfc9562('1d19dad6-ba7b-6811-80b4-1445FD782ba3');
        self::assertEquals('14:45:fd:78:2b:a3', $uuid->getNode()->toString());
    }

    public function testClockSequence(): void
    {
        /** @var UuidV6 $uuid */
        $uuid = UuidParser::fromRfc9562('1d19dad6-ba7b-6811-9846-00c04fd430c8');
        self::assertEquals(0x1846, $uuid->getClockSequence());

        /** @var UuidV6 $uuid */
        $uuid = UuidParser::fromRfc9562('1d19dad6-ba7b-6811-bfb9-00c04fd430c8');
        self::assertEquals(0x3fb9, $uuid->getClockSequence());
    }

    public function testToUuidV1(): void
    {
        $uuid6 = new UuidV6('1d19dad6ba7b681180b400c04fd430c8');
        $uuid1 = UuidNamespace::URL->getUuid();

        self::assertEquals($uuid1, $uuid6->toUuidV1());

        // fields do not change
        self::assertEquals($uuid1->getDateTime(), $uuid6->getDateTime());
        self::assertEquals($uuid1->getClockSequence(), $uuid6->getClockSequence());
        self::assertEquals($uuid1->getNode(), $uuid6->getNode());
    }

    public function testFactory(): void
    {
        $clock = new StaticClock(
            new \DateTimeImmutable('2023-10-29 17:04:00.123456 UTC'), // 1ee767d27875680
        );
        $rand = new Randomizer(new Xoshiro256StarStar(123)); // f969a0d1a18f5a32
        $node = StaticNode::fromHex('1234567890ab'); // 1334567890ab

        $uuid = UuidFactory::v6($node, null, $clock, $rand);
        self::assertEquals('1ee767d2-7875-6680-a9f9-1334567890ab', $uuid->toString());

        $fixedCS = 0xabcd & 0x3fff;

        // time underflow
        $clock = new StaticClock(new \DateTimeImmutable('1585-01-01 1:02 UTC')); // 0027bbfb17ab400
        $uuid = UuidFactory::v6($node, $fixedCS, $clock);
        self::assertEquals('0027bbfb-17ab-6400-abcd-1334567890ab', $uuid->toString());

        // time overflow
        $clock = new StaticClock(new \DateTimeImmutable('6000-01-01 2:03 UTC')); // [1]358432968ac2200
        $uuid = UuidFactory::v6($node, $fixedCS, $clock);
        self::assertEquals('35843296-8ac2-6200-abcd-1334567890ab', $uuid->toString());
    }

    public function testSystemTime(): void
    {
        $time1 = new \DateTime();
        $uuid = UuidFactory::v6();
        $time2 = new \DateTime();

        self::assertGreaterThanOrEqual($time1, $uuid->getDateTime());
        self::assertLessThanOrEqual($time2, $uuid->getDateTime());
    }

    public function testEquivalentToV1(): void
    {
        // V1 and V6 on the same node at the same time should be the same
        $clock = new StaticClock();
        $randEngine = new Xoshiro256StarStar();
        $randomizer1 = new Randomizer(clone $randEngine);
        $randomizer6 = new Randomizer(clone $randEngine);
        $node = StaticNode::fromHex('1234567890ab');
        $clockSeq = 0x123;

        $uuid1 = UuidFactory::v1($node, $clockSeq, $clock, $randomizer1);
        $uuid6 = UuidFactory::v6($node, $clockSeq, $clock, $randomizer6);

        self::assertEquals($uuid1->toString(), $uuid6->toUuidV1()->toString());
    }

    public function testRfcExample(): void
    {
        $time = new \DateTime('February 22, 2022 2:22:22.000000PM GMT-05:00');
        $node = StaticNode::fromHex('9E6BDECED846');
        $clockSeq = 0b11 << 12 | 0x3c8;

        $uuid = UuidFactory::v6($node, $clockSeq, $time);
        self::assertEquals('1EC9414C-232A-6B00-B3C8-9F6BDECED846', strtoupper($uuid->toString()));
    }

    // if RNG was overridden in a factory, do not use global randomizer for the node
    public function testRandomizerOverridden(): void
    {
        $engine = new Xoshiro256StarStar(); // any seed
        $r1 = new Randomizer(clone $engine);
        $r2 = new Randomizer(clone $engine);

        $clock = new StaticClock();

        $uuid1 = UuidFactory::v6(timestamp: $clock, randomizer: $r1)->toString();
        $uuid2 = UuidFactory::v6(node: new RandomNode($r2), timestamp: $clock, randomizer: $r2)->toString();

        self::assertEquals($uuid1, $uuid2);
    }

    public function testClockSeqTooLow(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("Clock sequence must be in range 0-16'383");

        UuidFactory::v6(clockSequence: -1);
    }

    public function testClockSeqTooHigh(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("Clock sequence must be in range 0-16'383");

        UuidFactory::v6(clockSequence: 10000000);
    }
}
