<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Clock\MutableClock;
use Arokettu\Clock\StaticClock;
use Arokettu\Clock\TickingClock;
use Arokettu\Uuid\Tests\Helper\FixedSequenceEngine;
use Arokettu\Uuid\UlidFactory;
use PHPUnit\Framework\TestCase;
use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

class UlidSequenceTest extends TestCase
{
    public function testSequences(): void
    {
        $sequences = [
            ['num' => 8, 'reserve' => true, 'v7' => false, 'uuids' => [
                '01963972-0580-9999-9999-999999199999', '01963972-0580-9999-9999-99999919999a',
                '01963972-0580-9999-9999-99999919999b', '01963972-0580-9999-9999-99999919999c',
                '01963972-0580-9999-9999-99999919999d', '01963972-0580-9999-9999-99999919999e',
                '01963972-0580-9999-9999-99999919999f', '01963972-0580-9999-9999-9999991999a0',
                // skip
                '01963972-0968-9999-9999-999999199999', '01963972-0968-9999-9999-99999919999a',
                '01963972-0968-9999-9999-99999919999b', '01963972-0968-9999-9999-99999919999c',
                '01963972-0968-9999-9999-99999919999d', '01963972-0968-9999-9999-99999919999e',
                '01963972-0968-9999-9999-99999919999f', '01963972-0968-9999-9999-9999991999a0',
            ]],
            ['num' => 8, 'reserve' => false, 'v7' => false, 'uuids' => [
                '01963972-0580-9999-9999-999999999999', '01963972-0580-9999-9999-99999999999a',
                '01963972-0580-9999-9999-99999999999b', '01963972-0580-9999-9999-99999999999c',
                '01963972-0580-9999-9999-99999999999d', '01963972-0580-9999-9999-99999999999e',
                '01963972-0580-9999-9999-99999999999f', '01963972-0580-9999-9999-9999999999a0',
                // skip
                '01963972-0968-9999-9999-999999999999', '01963972-0968-9999-9999-99999999999a',
                '01963972-0968-9999-9999-99999999999b', '01963972-0968-9999-9999-99999999999c',
                '01963972-0968-9999-9999-99999999999d', '01963972-0968-9999-9999-99999999999e',
                '01963972-0968-9999-9999-99999999999f', '01963972-0968-9999-9999-9999999999a0',
            ]],
            ['num' => 8, 'reserve' => true, 'v7' => true, 'uuids' => [
                '01963972-0580-7999-9999-999999199999', '01963972-0580-7999-9999-99999919999a',
                '01963972-0580-7999-9999-99999919999b', '01963972-0580-7999-9999-99999919999c',
                '01963972-0580-7999-9999-99999919999d', '01963972-0580-7999-9999-99999919999e',
                '01963972-0580-7999-9999-99999919999f', '01963972-0580-7999-9999-9999991999a0',
                // skip
                '01963972-0968-7999-9999-999999199999', '01963972-0968-7999-9999-99999919999a',
                '01963972-0968-7999-9999-99999919999b', '01963972-0968-7999-9999-99999919999c',
                '01963972-0968-7999-9999-99999919999d', '01963972-0968-7999-9999-99999919999e',
                '01963972-0968-7999-9999-99999919999f', '01963972-0968-7999-9999-9999991999a0',
            ]],
            ['num' => 8, 'reserve' => false, 'v7' => true, 'uuids' => [
                '01963972-0580-7999-9999-999999999999', '01963972-0580-7999-9999-99999999999a',
                '01963972-0580-7999-9999-99999999999b', '01963972-0580-7999-9999-99999999999c',
                '01963972-0580-7999-9999-99999999999d', '01963972-0580-7999-9999-99999999999e',
                '01963972-0580-7999-9999-99999999999f', '01963972-0580-7999-9999-9999999999a0',
                // skip
                '01963972-0968-7999-9999-999999999999', '01963972-0968-7999-9999-99999999999a',
                '01963972-0968-7999-9999-99999999999b', '01963972-0968-7999-9999-99999999999c',
                '01963972-0968-7999-9999-99999999999d', '01963972-0968-7999-9999-99999999999e',
                '01963972-0968-7999-9999-99999999999f', '01963972-0968-7999-9999-9999999999a0',
            ]],
        ];

        foreach ($sequences as ['num' => $num, 'reserve' => $reserve, 'v7' => $v7, 'uuids' => $uuids]) {
            $clock = new MutableClock(new \DateTime('2025-04-15 12:34:56'));
            $randomizer = new Randomizer(new FixedSequenceEngine("\x99"));

            $seq = UlidFactory::ulidSequence($v7, $reserve, $clock, $randomizer);

            for ($i = 0; $i < $num; $i++) {
                self::assertEquals($uuids[$i], $seq->next()->toRfc4122(), "reserve: $reserve, i: $i");
            }

            $clock->dateTime->modify('+1 sec');

            for ($i = 0; $i < $num; $i++) {
                self::assertEquals($uuids[$i + $num], $seq->next()->toRfc4122(), "reserve: $reserve, i2: $i");
            }
        }
    }

    public function testProperRandomizer(): void
    {
        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = new StaticClock(new \DateTime('2039-09-07 15:47:35.552'));

        $sequence = UlidFactory::ulidSequence(true, true, $clock, $randomizer);

        self::assertEquals('02000000-0000-7969-a0d1-a18f5a325e4d', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7969-a0d1-a18f5a325e4e', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7969-a0d1-a18f5a325e4f', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7969-a0d1-a18f5a325e50', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7969-a0d1-a18f5a325e51', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7969-a0d1-a18f5a325e52', $sequence->next()->toRfc4122());

        $randomizer = new Randomizer(new Xoshiro256StarStar(123));

        $sequence = UlidFactory::ulidSequence(false, true, $clock, $randomizer);

        self::assertEquals('02000000-0000-f969-a0d1-a18f5a325e4d', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-f969-a0d1-a18f5a325e4e', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-f969-a0d1-a18f5a325e4f', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-f969-a0d1-a18f5a325e50', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-f969-a0d1-a18f5a325e51', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-f969-a0d1-a18f5a325e52', $sequence->next()->toRfc4122());
    }

    public function testProperRandomizerWithAdvance(): void
    {
        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = new MutableClock(new \DateTime('2039-09-07 15:47:35.552'));

        $sequence = UlidFactory::ulidSequence(false, true, $clock, $randomizer);

        self::assertEquals('02000000-0000-f969-a0d1-a18f5a325e4d', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-f969-a0d1-a18f5a325e4e', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-f969-a0d1-a18f5a325e4f', $sequence->next()->toRfc4122());

        $clock->dateTime->modify('+1 second');

        self::assertEquals('02000000-03e8-2fa6-f2c3-462baa770682', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-03e8-2fa6-f2c3-462baa770683', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-03e8-2fa6-f2c3-462baa770684', $sequence->next()->toRfc4122());

        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = new MutableClock(new \DateTime('2039-09-07 15:47:35.552'));

        $sequence = UlidFactory::ulidSequence(true, true, $clock, $randomizer);

        self::assertEquals('02000000-0000-7969-a0d1-a18f5a325e4d', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7969-a0d1-a18f5a325e4e', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7969-a0d1-a18f5a325e4f', $sequence->next()->toRfc4122());

        $clock->dateTime->modify('+1 second');

        self::assertEquals('02000000-03e8-7fa6-b2c3-462baa770682', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-03e8-7fa6-b2c3-462baa770683', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-03e8-7fa6-b2c3-462baa770684', $sequence->next()->toRfc4122());
    }

    public function testProperRandomizerWithAdvanceEveryStep(): void
    {
        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = new TickingClock(
            \DateInterval::createFromDateString('+128 msec'),
            new \DateTime('2039-09-07 15:47:35.552'),
        );

        $sequence = UlidFactory::ulidSequence(false, true, $clock, $randomizer);

        self::assertEquals('02000000-0000-f969-a0d1-a18f5a325e4d', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0080-2fa6-f2c3-462baa770682', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0100-de78-9d95-b3d878566a28', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0180-1b75-f844-9b23c260551a', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0200-d4df-5c6d-af02d342705c', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0280-ba37-4ea8-3797f7264d48', $sequence->next()->toRfc4122());

        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = new TickingClock(
            \DateInterval::createFromDateString('+128 msec'),
            new \DateTime('2039-09-07 15:47:35.552'),
        );

        $sequence = UlidFactory::ulidSequence(true, true, $clock, $randomizer);

        self::assertEquals('02000000-0000-7969-a0d1-a18f5a325e4d', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0080-7fa6-b2c3-462baa770682', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0100-7e78-9d95-b3d878566a28', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0180-7b75-b844-9b23c260551a', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0200-74df-9c6d-af02d342705c', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0280-7a37-8ea8-3797f7264d48', $sequence->next()->toRfc4122());
    }

    public function testOverflowWorstCase(): void
    {
        $randomizer = new Randomizer(new FixedSequenceEngine("\xff"));

        $sequence = UlidFactory::ulidSequence(false, false, new StaticClock(), $randomizer);
        $sequence->next();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Counter sequence overflow');
        $sequence->next();
    }

    public function testOverflowWorstCaseV7Compatible(): void
    {
        $randomizer = new Randomizer(new FixedSequenceEngine("\xff"));

        $sequence = UlidFactory::ulidSequence(true, false, new StaticClock(), $randomizer);
        $sequence->next();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Counter sequence overflow');
        $sequence->next();
    }

    public function testIterator(): void
    {
        $clock = new StaticClock();

        $seq1 = UlidFactory::ulidSequence(false, true, $clock, new Randomizer(new Xoshiro256StarStar(123)));
        $seq2 = UlidFactory::ulidSequence(false, true, $clock, new Randomizer(new Xoshiro256StarStar(123)));

        $counter = 10;
        foreach ($seq1 as $ulid) {
            self::assertEquals($seq2->next(), $ulid);
            $counter--;
            if (!$counter) {
                break;
            }
        }
    }
}
