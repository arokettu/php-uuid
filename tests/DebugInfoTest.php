<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Clock\StaticClock;
use Arokettu\Uuid\Nodes\RandomNode;
use Arokettu\Uuid\Nodes\RawNode;
use Arokettu\Uuid\UlidFactory;
use Arokettu\Uuid\UuidFactory;
use Arokettu\Uuid\UuidV2;
use PHPUnit\Framework\TestCase;
use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

class DebugInfoTest extends TestCase
{
    public function testV2(): void
    {
        $uuid = new UuidV2('000004d292e821ed81003fdb0085247e');

        $this->assertEquals([
            'version' => 2,
            'rfc' => '000004d2-92e8-21ed-8100-3fdb0085247e',
            'base32' => '00002D54Q847PR201ZVC08A93Y',
            'timestamp' => new \DateTimeImmutable('2023-01-13T02:14:24.842137+00:00'),
            'domain' => 0,
            'identifier' => 1234,
            'node' => new RawNode('3fdb0085247e'),
            'clockSequence' => 0x01,
        ], $uuid->__debugInfo());
    }

    public function testV4(): void
    {
        $rnd = new Randomizer(new Xoshiro256StarStar(456));

        $uuid = UuidFactory::v4($rnd);

        $this->assertEquals([
            'version' => 4,
            'rfc' => 'f12110cb-92a7-4394-b07a-cca458e99374',
            'base32' => '7H448CQ4N78EAB0YPCMHCEK4VM',
        ], $uuid->__debugInfo());
    }

    public function testV6(): void
    {
        $clock = StaticClock::fromTimestamp(1701977743);
        $rnd = new Randomizer(new Xoshiro256StarStar(456));
        $node = new RandomNode($rnd);

        $uuid = UuidFactory::v6($node, null, $clock, $rnd);

        $this->assertEquals([
            'version' => 6,
            'rfc' => '1ee9537c-f605-6180-a1f1-317acca458e9',
            'base32' => '0YX59QSXG5C60A3W9HFB6A8P79',
            'timestamp' => new \DateTimeImmutable('@1701977743'),
            'node' => new RawNode('317acca458e9'),
            'clockSequence' => 0x21f1,
        ], $uuid->__debugInfo());
    }

    public function testV7(): void
    {
        $clock = StaticClock::fromTimestamp(1701977743);
        $rnd = new Randomizer(new Xoshiro256StarStar(456));

        $ulid = UlidFactory::ulid(false, $clock, $rnd);

        $this->assertEquals([
            'rfc' => '018c45c7-5e98-f121-10cb-92a74394307a',
            'base32' => '01HH2WEQMRY4GH1JWJMX1S8C3T',
            'timestamp' => new \DateTimeImmutable('@1701977743'),
        ], $ulid->__debugInfo());
    }
}
