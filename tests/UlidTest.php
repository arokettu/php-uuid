<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Clock\RoundingClock;
use Arokettu\Clock\StaticClock;
use Arokettu\Clock\SystemClock;
use Arokettu\Uuid\Tests\Helper\FixedSequenceEngine;
use Arokettu\Uuid\UlidFactory;
use PHPUnit\Framework\TestCase;
use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

class UlidTest extends TestCase
{
    public function testMin(): void
    {
        $uuid = UlidFactory::ulid(
            false,
            new StaticClock(new \DateTime('@0')),
            new Randomizer(new FixedSequenceEngine("\0")),
        );

        self::assertEquals('00000000000000000000000000', $uuid->toString());
    }

    public function testMax(): void
    {
        $uuid = UlidFactory::ulid(
            false,
            new StaticClock(new \DateTime('@281474976710.655')),
            new Randomizer(new FixedSequenceEngine("\xff")),
        );

        self::assertEquals('7ZZZZZZZZZZZZZZZZZZZZZZZZZ', $uuid->toString());
    }

    public function testRandom(): void
    {
        $uuid = UlidFactory::ulid(
            false,
            new StaticClock(new \DateTime('@1700000000.000')), // f969a0d1a18f5a325e4d6d65c7e335f8
            new Randomizer(new Xoshiro256StarStar(123)), // 18bcfe56800
        );

        self::assertEquals('01HF7YAT00Z5MT1MD1HXD34QJD', $uuid->toString());
    }

    public function testRollover(): void
    {
        $uuid = UlidFactory::ulid(
            false,
            new StaticClock(new \DateTime('@281474976710.656')),
            new Randomizer(new FixedSequenceEngine("\x7b\xde\xf7\xbd\xef")), // 281474976710.655 + 0.001
        );

        self::assertEquals('0000000000FFFFFFFFFFFFFFFF', $uuid->toString());

        $uuid = UlidFactory::ulid(
            false,
            new StaticClock(new \DateTime('@281474976710.657')),
            new Randomizer(new FixedSequenceEngine("\x7b\xde\xf7\xbd\xef")), // 281474976710.655 + 0.001 + 0.001
        );

        self::assertEquals('0000000001FFFFFFFFFFFFFFFF', $uuid->toString());
    }

    public function testTime(): void
    {
        // v7 has millisecond precision, we have to round our raw timestamp
        $clock = new RoundingClock(new StaticClock(), RoundingClock::ROUND_MILLISECONDS);
        $ulid = UlidFactory::ulid(timestamp: $clock);

        self::assertEquals($clock->now(), $ulid->getDateTime());
    }

    public function testSystemTime(): void
    {
        // v7 has millisecond precision, we have to round our raw timestamp
        $clock = new RoundingClock(new SystemClock(), RoundingClock::ROUND_MILLISECONDS);

        $before = $clock->now();
        $ulid = UlidFactory::ulid();
        $after = $clock->now();

        self::assertGreaterThanOrEqual($before, $ulid->getDateTime());
        self::assertLessThanOrEqual($after, $ulid->getDateTime());
    }
}
