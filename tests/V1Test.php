<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Clock\StaticClock;
use Arokettu\Uuid\Node\StaticNode;
use Arokettu\Uuid\UuidFactory;
use Arokettu\Uuid\UuidNamespaces;
use Arokettu\Uuid\UuidV1;
use PHPUnit\Framework\TestCase;
use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

class V1Test extends TestCase
{
    public function testTimestamp(): void
    {
        $uuid = UuidNamespaces::url();
        $ts = new \DateTime('1998-02-04T22:13:53.151182+0000');
        self::assertEquals($ts, $uuid->getDateTime());

        // check with the same timestamp as UUIDv2 example
        // {00000002-92e8-11ed-8100-3fdb0085247e}
        $uuid = new UuidV1('0000000292e811ed81003fdb0085247e');
        $ts = new \DateTime('2023-01-13T02:14:24.842137+0000');
        self::assertEquals($ts, $uuid->getDateTime());
    }

    public function testToUuidV6(): void
    {
        $uuid = UuidNamespaces::url();
        self::assertEquals('1d19dad6-ba7b-6811-80b4-00c04fd430c8', $uuid->toUuidV6()->toString());
    }

    public function testFactory(): void
    {
        $clock = new StaticClock(new \DateTimeImmutable('2023-10-29 17:04 UTC'));
        $randEngine = new Xoshiro256StarStar(123);
        $node = StaticNode::fromHex('1234567890ab');
    }

    public function testEquivalentToV6(): void
    {
        // V1 and V6 on the same node at the same time should be the same
        $clock = new StaticClock();
        $randEngine = new Xoshiro256StarStar();
        $randomizer1 = new Randomizer(clone $randEngine);
        $randomizer6 = new Randomizer(clone $randEngine);
        $node = StaticNode::fromHex('1234567890ab');

        $uuid1 = UuidFactory::v1($node, $clock, $randomizer1);
        $uuid6 = UuidFactory::v6($node, $clock, $randomizer6);

        self::assertEquals($uuid6->toString(), $uuid1->toUuidV6()->toString());
    }
}
