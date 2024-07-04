<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Clock\RoundingClock;
use Arokettu\Clock\StaticClock;
use Arokettu\Clock\SystemClock;
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
            new StaticClock(new \DateTime('@0')),
            new Randomizer(new FixedSequenceEngine("\0")),
        );

        self::assertEquals('00000000-0000-7000-8000-000000000000', $uuid->toString());
    }

    public function testMax(): void
    {
        $uuid = UuidFactory::v7(
            new StaticClock(new \DateTime('@281474976710.655')),
            new Randomizer(new FixedSequenceEngine("\xff")),
        );

        self::assertEquals('ffffffff-ffff-7fff-bfff-ffffffffffff', $uuid->toString());
    }

    public function testRandom(): void
    {
        $uuid = UuidFactory::v7(
            new StaticClock(new \DateTime('@1700000000.000')), // 18bcfe56800
            new Randomizer(new Xoshiro256StarStar(123)), // f969a0d1a18f5a32 5e4d6d65c7e335f8
        );

        self::assertEquals('018bcfe5-6800-7969-a0d1-a18f5a325e4d', $uuid->toString());
    }

    public function testRollover(): void
    {
        $uuid = UuidFactory::v7(
            new StaticClock(new \DateTime('@281474976710.656')),
            new Randomizer(new FixedSequenceEngine("\x88")), // 281474976710.655 + 0.001
        );

        self::assertEquals('00000000-0000-7888-8888-888888888888', $uuid->toString());

        $uuid = UuidFactory::v7(
            new StaticClock(new \DateTime('@281474976710.657')),
            new Randomizer(new FixedSequenceEngine("\x88")), // 281474976710.655 + 0.001 + 0.001
        );

        self::assertEquals('00000000-0001-7888-8888-888888888888', $uuid->toString());
    }

    public function testTime(): void
    {
        // v7 has millisecond precision, we have to round our raw timestamp
        $clock = new RoundingClock(new StaticClock(), RoundingClock::ROUND_MILLISECONDS);
        $uuid = UuidFactory::v7(time: $clock);

        self::assertEquals($clock->now(), $uuid->getDateTime());
    }

    public function testSystemTime(): void
    {
        // v7 has millisecond precision, we have to round our raw timestamp
        $clock = new RoundingClock(new SystemClock(), RoundingClock::ROUND_MILLISECONDS);

        $before = $clock->now();
        $uuid = UuidFactory::v7();
        $after = $clock->now();

        self::assertGreaterThanOrEqual($before, $uuid->getDateTime());
        self::assertLessThanOrEqual($after, $uuid->getDateTime());
    }

    public function testRfcExample(): void
    {
        $time = new \DateTime('February 22, 2022 2:22:22.000000PM GMT-05:00');
        $rnd = new Randomizer(new FixedSequenceEngine(hex2bin('0CC3'), hex2bin('18C4DC0C0C07398F')));
        $uuid = UuidFactory::v7(new StaticClock($time), $rnd);

        self::assertEquals('017F22E2-79B0-7CC3-98C4-DC0C0C07398F', strtoupper($uuid->toString()));
    }
}
