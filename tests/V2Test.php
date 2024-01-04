<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Clock\StaticClock;
use Arokettu\Uuid\DceSecurity\Domains;
use Arokettu\Uuid\Nodes\RandomNode;
use Arokettu\Uuid\Nodes\StaticNode;
use Arokettu\Uuid\Tests\Helper\FixedSequenceEngine;
use Arokettu\Uuid\UuidFactory;
use Arokettu\Uuid\UuidV2;
use PHPUnit\Framework\TestCase;
use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

class V2Test extends TestCase
{
    public function testTimestamp(): void
    {
        // rare example found in the internet wilds
        // {000004d2-92e8-21ed-8100-3fdb0085247e}
        $uuid = new UuidV2('000004d292e821ed81003fdb0085247e');
        $ts = new \DateTime('2023-01-13T02:14:24.842137+0000');

        self::assertEquals($ts, $uuid->getDateTime());
    }
    public function testFields(): void
    {
        // rare example found in the internet wilds
        // {000004d2-92e8-21ed-8100-3fdb0085247e}
        $uuid = new UuidV2('000004d292e821ed81003fdb0085247e');

        self::assertEquals(0, $uuid->getDomain());
        self::assertEquals(1234, $uuid->getIdentifier());
    }

    public function testFactory(): void
    {
        $clock = StaticClock::fromDateString('2023-10-29 17:04:00.123456 UTC'); // 1ee767d27875680
        $rand = new Randomizer(new Xoshiro256StarStar(123)); // f969a0d1a18f5a32
        $node = StaticNode::fromHex('1234567890ab'); // 1334567890ab

        // domain: 00, identifier: 0x18894
        $uuid = UuidFactory::v2(Domains::PERSON, 100500, $node, $clock, $rand);
        self::assertEquals('00018894-767d-21ee-b900-1334567890ab', $uuid->toString());

        $fixed = new Randomizer(new FixedSequenceEngine("\xab\xcd\xef"));

        // time underflow
        $clock = new StaticClock(new \DateTimeImmutable('1585-01-01 1:02 UTC')); // 0027bbfb17ab400
        // domain: 01, identifier: 0x100500
        $uuid = UuidFactory::v2(Domains::GROUP, 1049856, $node, $clock, $fixed);
        self::assertEquals('00100500-7bbf-2002-ab01-1334567890ab', $uuid->toString());

        // time overflow
        $clock = new StaticClock(new \DateTimeImmutable('6000-01-01 2:03 UTC')); // [1]358432968ac2200
        // domain: 02, identifier: 0xdedbeef
        $uuid = UuidFactory::v2(Domains::ORG, 0xdedbeef, $node, $clock, $fixed);
        self::assertEquals('0dedbeef-4329-2358-ab02-1334567890ab', $uuid->toString());
    }

    public function testNoNegativeDomain(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Domain must be in range 0-255');
        UuidFactory::v2(-1, 0);
    }

    public function testOverflowDomain(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Domain must be in range 0-255');
        UuidFactory::v2(256, 0);
    }

    public function testNoNegativeIdentifier(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("Identifier must be in range 0-4'294'967'295");
        UuidFactory::v2(0, -1);
    }

    public function testOverflowIdentifier(): void
    {
        if (PHP_INT_SIZE < 8) {
            $this->markTestSkipped();
        }

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("Identifier must be in range 0-4'294'967'295");
        UuidFactory::v2(0, 4_294_967_296);
    }

    public function testSystemTime(): void
    {
        $time1 = new \DateTime('-8 min'); // see dt truncation
        $uuid = UuidFactory::v2(0, 0);
        $time2 = new \DateTime();

        self::assertGreaterThanOrEqual($time1, $uuid->getDateTime());
        self::assertLessThanOrEqual($time2, $uuid->getDateTime());
    }

    // if RNG was overridden in a factory, do not use global randomizer for the node
    public function testRandomizerOverridden(): void
    {
        $engine = new Xoshiro256StarStar(); // any seed
        $r1 = new Randomizer(clone $engine);
        $r2 = new Randomizer(clone $engine);
        $r3 = new Randomizer(clone $engine);

        $clock = new StaticClock();

        $uuid1 = UuidFactory::v2(0, 1, clock: $clock, randomizer: $r1)->toString();
        $uuid2 = UuidFactory::v2(0, 1, node: new RandomNode($r2), clock: $clock, randomizer: $r2)->toString();
        // for a single generation this should work too
        $uuid3 = UuidFactory::v2(0, 1, node: StaticNode::random($r3), clock: $clock, randomizer: $r3)->toString();

        self::assertEquals($uuid1, $uuid2);
        self::assertEquals($uuid1, $uuid3);
    }
}
