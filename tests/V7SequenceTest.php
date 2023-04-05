<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Clock\MutableClock;
use Arokettu\Clock\StaticClock;
use Arokettu\Clock\TickingClock;
use Arokettu\Uuid\Tests\Helper\FixedSequenceEngine;
use Arokettu\Uuid\UuidFactory;
use PHPUnit\Framework\TestCase;
use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

class V7SequenceTest extends TestCase
{
    public function testSequences(): void
    {
        $sequences = [
            ['num' => 1, 'bits' => 0, 'uuids' => [
                '01963972-0580-7fff-bfff-ffffffffffff',
                // skip
                '01963972-0968-7fff-bfff-ffffffffffff',
            ]],
            ['num' => 4, 'bits' => 2, 'uuids' => [
                '01963972-0580-73ff-bfff-ffffffffffff', '01963972-0580-77ff-bfff-ffffffffffff',
                '01963972-0580-7bff-bfff-ffffffffffff', '01963972-0580-7fff-bfff-ffffffffffff',
                // skip
                '01963972-0968-73ff-bfff-ffffffffffff', '01963972-0968-77ff-bfff-ffffffffffff',
                '01963972-0968-7bff-bfff-ffffffffffff', '01963972-0968-7fff-bfff-ffffffffffff',
            ]],
            ['num' => 8, 'bits' => 4, 'uuids' => [
                '01963972-0580-70ff-bfff-ffffffffffff', '01963972-0580-71ff-bfff-ffffffffffff',
                '01963972-0580-72ff-bfff-ffffffffffff', '01963972-0580-73ff-bfff-ffffffffffff',
                '01963972-0580-74ff-bfff-ffffffffffff', '01963972-0580-75ff-bfff-ffffffffffff',
                '01963972-0580-76ff-bfff-ffffffffffff', '01963972-0580-77ff-bfff-ffffffffffff',
                // skip
                '01963972-0968-70ff-bfff-ffffffffffff', '01963972-0968-71ff-bfff-ffffffffffff',
                '01963972-0968-72ff-bfff-ffffffffffff', '01963972-0968-73ff-bfff-ffffffffffff',
                '01963972-0968-74ff-bfff-ffffffffffff', '01963972-0968-75ff-bfff-ffffffffffff',
                '01963972-0968-76ff-bfff-ffffffffffff', '01963972-0968-77ff-bfff-ffffffffffff',
            ]],
            ['num' => 8, 'bits' => 7, 'uuids' => [
                '01963972-0580-701f-bfff-ffffffffffff', '01963972-0580-703f-bfff-ffffffffffff',
                '01963972-0580-705f-bfff-ffffffffffff', '01963972-0580-707f-bfff-ffffffffffff',
                '01963972-0580-709f-bfff-ffffffffffff', '01963972-0580-70bf-bfff-ffffffffffff',
                '01963972-0580-70df-bfff-ffffffffffff', '01963972-0580-70ff-bfff-ffffffffffff',
                // skip
                '01963972-0968-701f-bfff-ffffffffffff', '01963972-0968-703f-bfff-ffffffffffff',
                '01963972-0968-705f-bfff-ffffffffffff', '01963972-0968-707f-bfff-ffffffffffff',
                '01963972-0968-709f-bfff-ffffffffffff', '01963972-0968-70bf-bfff-ffffffffffff',
                '01963972-0968-70df-bfff-ffffffffffff', '01963972-0968-70ff-bfff-ffffffffffff',
            ]],
            ['num' => 8, 'bits' => 12, 'uuids' => [
                '01963972-0580-7000-bfff-ffffffffffff', '01963972-0580-7001-bfff-ffffffffffff',
                '01963972-0580-7002-bfff-ffffffffffff', '01963972-0580-7003-bfff-ffffffffffff',
                '01963972-0580-7004-bfff-ffffffffffff', '01963972-0580-7005-bfff-ffffffffffff',
                '01963972-0580-7006-bfff-ffffffffffff', '01963972-0580-7007-bfff-ffffffffffff',
                // skip
                '01963972-0968-7000-bfff-ffffffffffff', '01963972-0968-7001-bfff-ffffffffffff',
                '01963972-0968-7002-bfff-ffffffffffff', '01963972-0968-7003-bfff-ffffffffffff',
                '01963972-0968-7004-bfff-ffffffffffff', '01963972-0968-7005-bfff-ffffffffffff',
                '01963972-0968-7006-bfff-ffffffffffff', '01963972-0968-7007-bfff-ffffffffffff',
            ]],
        ];

        foreach ($sequences as ['num' => $num, 'bits' => $bits, 'uuids' => $uuids]) {
            $clock = new MutableClock(new \DateTime('2025-04-15 12:34:56'));
            $randomizer = new Randomizer(new FixedSequenceEngine("\xff"));

            $seq = UuidFactory::v7sequence($bits, $randomizer, $clock);

            for ($i = 0; $i < $num; $i++) {
                self::assertEquals($uuids[$i], $seq->next()->toRfc4122(), "bits: $bits, i: $i");
            }

            $clock->dateTime->modify('+1 sec');

            for ($i = 0; $i < $num; $i++) {
                self::assertEquals($uuids[$i + $num], $seq->next()->toRfc4122(), "bits: $bits, i2: $i");
            }
        }
    }

    public function testProperRandomizer(): void
    {
        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = new StaticClock(new \DateTime('2039-09-07 15:47:35.552'));

        $sequence = UuidFactory::v7sequence(8, $randomizer, $clock);

        self::assertEquals('02000000-0000-7009-a0d1-a18f5a325e4d', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7016-b2c3-462baa770682', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7028-9d95-b3d878566a28', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7035-b844-9b23c260551a', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-704f-9c6d-af02d3c2705c', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7057-8ea8-3797f7a64d48', $sequence->next()->toRfc4122());
    }

    public function testProperRandomizerWithAdvance(): void
    {
        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = new MutableClock(new \DateTime('2039-09-07 15:47:35.552'));

        $sequence = UuidFactory::v7sequence(8, $randomizer, $clock);

        self::assertEquals('02000000-0000-7009-a0d1-a18f5a325e4d', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7016-b2c3-462baa770682', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7028-9d95-b3d878566a28', $sequence->next()->toRfc4122());

        $clock->dateTime->modify('+1 second');

        self::assertEquals('02000000-03e8-7005-b844-9b23c260551a', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-03e8-701f-9c6d-af02d3c2705c', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-03e8-7027-8ea8-3797f7a64d48', $sequence->next()->toRfc4122());
    }

    public function testProperRandomizerWithAdvanceEveryStep(): void
    {
        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = new TickingClock(
            \DateInterval::createFromDateString('+128 msec'),
            new \DateTime('2039-09-07 15:47:35.552'),
        );

        $sequence = UuidFactory::v7sequence(8, $randomizer, $clock);

        self::assertEquals('02000000-0000-7009-a0d1-a18f5a325e4d', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0080-7006-b2c3-462baa770682', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0100-7008-9d95-b3d878566a28', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0180-7005-b844-9b23c260551a', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0200-700f-9c6d-af02d3c2705c', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0280-7007-8ea8-3797f7a64d48', $sequence->next()->toRfc4122());
    }
}
