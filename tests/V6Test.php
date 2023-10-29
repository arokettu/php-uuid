<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Clock\StaticClock;
use Arokettu\Uuid\Node\StaticNode;
use Arokettu\Uuid\UuidFactory;
use Arokettu\Uuid\UuidNamespaces;
use Arokettu\Uuid\UuidV6;
use PHPUnit\Framework\TestCase;
use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

class V6Test extends TestCase
{
    public function testTimestamp(): void
    {
        // {1d19dad6-ba7b-6811-80b4-00c04fd430c8}
        $uuid = new UuidV6('1d19dad6ba7b681180b400c04fd430c8');
        $ts = new \DateTime('1998-02-04T22:13:53.151182+0000');
        self::assertEquals($ts, $uuid->getDateTime());
    }

    public function testToUuidV1(): void
    {
        $uuid = new UuidV6('1d19dad6ba7b681180b400c04fd430c8');
        self::assertEquals(UuidNamespaces::url(), $uuid->toUuidV1());
    }

    public function testFactory(): void
    {
        $clock = new StaticClock(new \DateTimeImmutable('2023-10-29 17:04 UTC'));
    }

    public function testEquivalentToV1(): void
    {
        // V1 and V6 on the same node at the same time should be the same
        $clock = new StaticClock();
        $randEngine = new Xoshiro256StarStar();
        $randomizer1 = new Randomizer(clone $randEngine);
        $randomizer6 = new Randomizer(clone $randEngine);
        $node = StaticNode::fromHex('1234567890ab');

        $uuid1 = UuidFactory::v1($node, $clock, $randomizer1);
        $uuid6 = UuidFactory::v6($node, $clock, $randomizer6);

        self::assertEquals($uuid1->toString(), $uuid6->toUuidV1()->toString());
    }
}
