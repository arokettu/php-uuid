<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Clock\RoundingClock;
use Arokettu\Clock\StaticClock;
use Arokettu\Uuid\Tests\Helper\FixedSequenceEngine;
use Arokettu\Uuid\UuidFactory;
use PHPUnit\Framework\TestCase;
use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

class V7Test extends TestCase
{
    public function testMin(): void
    {
        $uuid = UuidFactory::v7(
            new Randomizer(new FixedSequenceEngine("\0")),
            new StaticClock(new \DateTime('@0')),
        );

        self::assertEquals('00000000-0000-7000-8000-000000000000', $uuid->toString());
    }

    public function testMax(): void
    {
        $uuid = UuidFactory::v7(
            new Randomizer(new FixedSequenceEngine("\xff")),
            new StaticClock(new \DateTime('@281474976710.655')),
        );

        self::assertEquals('ffffffff-ffff-7fff-bfff-ffffffffffff', $uuid->toString());
    }

    public function testRandom(): void
    {
        $uuid = UuidFactory::v7(
            new Randomizer(new Xoshiro256StarStar(123)), // f969a0d1a18f5a325e4d6d65c7e335f8
            new StaticClock(new \DateTime('@1700000000.000')), // 18bcfe56800
        );

        self::assertEquals('018bcfe5-6800-7969-a0d1-a18f5a325e4d', $uuid->toString());
    }

    public function testRollover(): void
    {
        $uuid = UuidFactory::v7(
            new Randomizer(new FixedSequenceEngine("\x88")),
            new StaticClock(new \DateTime('@281474976710.656')), // 281474976710.655 + 0.001
        );

        self::assertEquals('00000000-0000-7888-8888-888888888888', $uuid->toString());

        $uuid = UuidFactory::v7(
            new Randomizer(new FixedSequenceEngine("\x88")),
            new StaticClock(new \DateTime('@281474976710.657')), // 281474976710.655 + 0.001 + 0.001
        );

        self::assertEquals('00000000-0001-7888-8888-888888888888', $uuid->toString());
    }

    public function testTime(): void
    {
        // v7 has millisecond precision, we have to round our raw timestamp
        $clock = new RoundingClock(new StaticClock(), RoundingClock::ROUND_MILLISECONDS);
        $uuid = UuidFactory::v7(clock: $clock);

        self::assertEquals($clock->now(), $uuid->getDateTime());
    }
}
