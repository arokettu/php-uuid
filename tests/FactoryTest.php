<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Clock\StaticClock;
use Arokettu\Uuid\ClockSequences\ClockSequence;
use Arokettu\Uuid\Nodes\StaticNode;
use Arokettu\Uuid\UlidFactory;
use Arokettu\Uuid\UuidFactory;
use PHPUnit\Framework\TestCase;
use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

class FactoryTest extends TestCase
{
    public function testPassDateTime(): void
    {
        $dt = new \DateTime('2024-06-27 19:55 EEST');
        $rnd = new Xoshiro256StarStar(12345);
        $node = StaticNode::random(new Randomizer(clone $rnd));

        self::assertEquals(
            'fd8e8a00-34a5-11ef-949b-9bd460413736',
            (string)UuidFactory::v1($node, ClockSequence::Random, $dt, new Randomizer(clone $rnd)),
        );
        self::assertEquals(
            '00000000-34a5-21ef-9b00-9bd460413736',
            (string)UuidFactory::v2(0, 0, $node, ClockSequence::Random, $dt, new Randomizer(clone $rnd)),
        );
        self::assertEquals(
            '1ef34a5f-d8e8-6a00-949b-9bd460413736',
            (string)UuidFactory::v6($node, ClockSequence::Random, $dt, new Randomizer(clone $rnd)),
        );
        self::assertEquals(
            '01905a9f-2ea0-7bd4-a041-37366abec688',
            (string)UuidFactory::v7($dt, new Randomizer(clone $rnd)),
        );
        self::assertEquals(
            '01J1D9YBN0KFA60G9Q6SNBXHM8',
            (string)UlidFactory::ulid(false, $dt, new Randomizer(clone $rnd)),
        );
    }

    public function testPassDateTimeImmutable(): void
    {
        $dt = new \DateTimeImmutable('2024-06-27 19:55 EEST');
        $rnd = new Xoshiro256StarStar(12345);
        $node = StaticNode::random(new Randomizer(clone $rnd));

        self::assertEquals(
            'fd8e8a00-34a5-11ef-949b-9bd460413736',
            (string)UuidFactory::v1($node, ClockSequence::Random, $dt, new Randomizer(clone $rnd)),
        );
        self::assertEquals(
            '00000000-34a5-21ef-9b00-9bd460413736',
            (string)UuidFactory::v2(0, 0, $node, ClockSequence::Random, $dt, new Randomizer(clone $rnd)),
        );
        self::assertEquals(
            '1ef34a5f-d8e8-6a00-949b-9bd460413736',
            (string)UuidFactory::v6($node, ClockSequence::Random, $dt, new Randomizer(clone $rnd)),
        );
        self::assertEquals(
            '01905a9f-2ea0-7bd4-a041-37366abec688',
            (string)UuidFactory::v7($dt, new Randomizer(clone $rnd)),
        );
        self::assertEquals(
            '01J1D9YBN0KFA60G9Q6SNBXHM8',
            (string)UlidFactory::ulid(false, $dt, new Randomizer(clone $rnd)),
        );
    }

    public function testPassClock(): void
    {
        $clock = StaticClock::fromDateString('2024-06-27 19:55 EEST');
        $rnd = new Xoshiro256StarStar(12345);
        $node = StaticNode::random(new Randomizer(clone $rnd));

        self::assertEquals(
            'fd8e8a00-34a5-11ef-949b-9bd460413736',
            (string)UuidFactory::v1($node, ClockSequence::Random, $clock, new Randomizer(clone $rnd)),
        );
        self::assertEquals(
            '00000000-34a5-21ef-9b00-9bd460413736',
            (string)UuidFactory::v2(0, 0, $node, ClockSequence::Random, $clock, new Randomizer(clone $rnd)),
        );
        self::assertEquals(
            '1ef34a5f-d8e8-6a00-949b-9bd460413736',
            (string)UuidFactory::v6($node, ClockSequence::Random, $clock, new Randomizer(clone $rnd)),
        );
        self::assertEquals(
            '01905a9f-2ea0-7bd4-a041-37366abec688',
            (string)UuidFactory::v7($clock, new Randomizer(clone $rnd)),
        );
        self::assertEquals(
            '01J1D9YBN0KFA60G9Q6SNBXHM8',
            (string)UlidFactory::ulid(false, $clock, new Randomizer(clone $rnd)),
        );
    }
}
