<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Clock\MutableClock;
use Arokettu\Clock\StaticClock;
use Arokettu\Clock\TickingClock;
use Arokettu\Uuid\SequenceFactory;
use Arokettu\Uuid\Tests\Helper\FixedSequenceEngine;
use PHPUnit\Framework\TestCase;
use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

class SequenceUlidTest extends TestCase
{
    public function testSequences(): void
    {
        $sequences = [
            ['num' => 8, 'v7' => false, 'uuids' => [
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
            ['num' => 8, 'v7' => true, 'uuids' => [
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

        foreach ($sequences as ['num' => $num, 'v7' => $v7, 'uuids' => $uuids]) {
            $clock = new MutableClock(new \DateTime('2025-04-15 12:34:56'));
            $randomizer = new Randomizer(new FixedSequenceEngine("\x99"));

            $seq = SequenceFactory::ulid($v7, $clock, $randomizer);

            for ($i = 0; $i < $num; $i++) {
                self::assertEquals($uuids[$i], $seq->next()->toRfc4122(), "i: $i");
            }

            $clock->dateTime->modify('+1 sec');

            for ($i = 0; $i < $num; $i++) {
                self::assertEquals($uuids[$i + $num], $seq->next()->toRfc4122(), "i2: $i");
            }
        }
    }

    public function testProperRandomizer(): void
    {
        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = new StaticClock(new \DateTime('2039-09-07 15:47:35.552'));

        $sequence = SequenceFactory::ulid(true, $clock, $randomizer);

        self::assertEquals('02000000-0000-7969-a0d1-a18f5a6d4d5e', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7969-a0d1-a18f5a6d4d5f', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7969-a0d1-a18f5a6d4d60', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7969-a0d1-a18f5a6d4d61', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7969-a0d1-a18f5a6d4d62', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7969-a0d1-a18f5a6d4d63', $sequence->next()->toRfc4122());

        $randomizer = new Randomizer(new Xoshiro256StarStar(123));

        $sequence = SequenceFactory::ulid(false, $clock, $randomizer);

        self::assertEquals('02000000-0000-f969-a0d1-a18f5a6d4d5e', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-f969-a0d1-a18f5a6d4d5f', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-f969-a0d1-a18f5a6d4d60', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-f969-a0d1-a18f5a6d4d61', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-f969-a0d1-a18f5a6d4d62', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-f969-a0d1-a18f5a6d4d63', $sequence->next()->toRfc4122());
    }

    public function testProperRandomizerWithAdvance(): void
    {
        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = new MutableClock(new \DateTime('2039-09-07 15:47:35.552'));

        $sequence = SequenceFactory::ulid(false, $clock, $randomizer);

        self::assertEquals('02000000-0000-f969-a0d1-a18f5a6d4d5e', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-f969-a0d1-a18f5a6d4d5f', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-f969-a0d1-a18f5a6d4d60', $sequence->next()->toRfc4122());

        $clock->dateTime->modify('+1 second');

        self::assertEquals('02000000-03e8-2fa6-f2c3-462baacf8206', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-03e8-2fa6-f2c3-462baacf8207', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-03e8-2fa6-f2c3-462baacf8208', $sequence->next()->toRfc4122());

        // if clock goes back, ignore the timestamp
        $clock->dateTime->modify('-5 seconds');

        self::assertEquals('02000000-03e8-2fa6-f2c3-462baacf8209', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-03e8-2fa6-f2c3-462baacf820a', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-03e8-2fa6-f2c3-462baacf820b', $sequence->next()->toRfc4122());

        // when time catches up, use it
        $clock->dateTime->modify('+10 seconds');

        self::assertEquals('02000000-1770-de78-9d95-b3d87829286a', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-1770-de78-9d95-b3d87829286b', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-1770-de78-9d95-b3d87829286c', $sequence->next()->toRfc4122());

        // the change below 1msec should not disrupt the counter
        $clock->dateTime->modify('+500 usec');

        self::assertEquals('02000000-1770-de78-9d95-b3d87829286d', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-1770-de78-9d95-b3d87829286e', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-1770-de78-9d95-b3d87829286f', $sequence->next()->toRfc4122());
    }

    public function testProperRandomizerWithAdvanceV7Compatible(): void
    {
        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = new MutableClock(new \DateTime('2039-09-07 15:47:35.552'));

        $sequence = SequenceFactory::ulid(true, $clock, $randomizer);

        self::assertEquals('02000000-0000-7969-a0d1-a18f5a6d4d5e', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7969-a0d1-a18f5a6d4d5f', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7969-a0d1-a18f5a6d4d60', $sequence->next()->toRfc4122());

        $clock->dateTime->modify('+1 second');

        self::assertEquals('02000000-03e8-7fa6-b2c3-462baacf8206', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-03e8-7fa6-b2c3-462baacf8207', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-03e8-7fa6-b2c3-462baacf8208', $sequence->next()->toRfc4122());

        // if clock goes back, ignore the timestamp
        $clock->dateTime->modify('-5 seconds');

        self::assertEquals('02000000-03e8-7fa6-b2c3-462baacf8209', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-03e8-7fa6-b2c3-462baacf820a', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-03e8-7fa6-b2c3-462baacf820b', $sequence->next()->toRfc4122());

        // when time catches up, use it
        $clock->dateTime->modify('+10 seconds');

        self::assertEquals('02000000-1770-7e78-9d95-b3d87829286a', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-1770-7e78-9d95-b3d87829286b', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-1770-7e78-9d95-b3d87829286c', $sequence->next()->toRfc4122());

        // the change below 1msec should not disrupt the counter
        $clock->dateTime->modify('+500 usec');

        self::assertEquals('02000000-1770-7e78-9d95-b3d87829286d', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-1770-7e78-9d95-b3d87829286e', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-1770-7e78-9d95-b3d87829286f', $sequence->next()->toRfc4122());
    }

    public function testProperRandomizerWithAdvanceEveryStep(): void
    {
        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = TickingClock::fromDateString('+128 msec', '2039-09-07 15:47:35.552');

        $sequence = SequenceFactory::ulid(false, $clock, $randomizer);

        self::assertEquals('02000000-0000-f969-a0d1-a18f5a6d4d5e', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0080-2fa6-f2c3-462baacf8206', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0100-de78-9d95-b3d87829286a', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0180-1b75-f844-9b23c27e1a55', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0200-d4df-5c6d-af02d3235c70', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0280-ba37-4ea8-3797f7f3484d', $sequence->next()->toRfc4122());

        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = TickingClock::fromDateString('+128 msec', '2039-09-07 15:47:35.552');

        $sequence = SequenceFactory::ulid(true, $clock, $randomizer);

        self::assertEquals('02000000-0000-7969-a0d1-a18f5a6d4d5e', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0080-7fa6-b2c3-462baacf8206', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0100-7e78-9d95-b3d87829286a', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0180-7b75-b844-9b23c27e1a55', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0200-74df-9c6d-af02d3235c70', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0280-7a37-8ea8-3797f7f3484d', $sequence->next()->toRfc4122());
    }

    public function testOverflow(): void
    {
        $randomizer = new Randomizer(new FixedSequenceEngine("\xf0\xfe\xff"));
        $clock = MutableClock::fromTimestamp(1698890500);

        $sequence = SequenceFactory::ulid(false, $clock, $randomizer);

        self::assertEquals('018b8dc3-c7a0-f0fe-fff0-fefff0fffef0', $sequence->next()->toRfc4122());

        for ($i = 0; $i < 269; $i++) { // roll to an overflow
            $sequence->next();
        }

        self::assertEquals('018b8dc3-c7a0-f0fe-fff0-fefff0fffffe', $sequence->next()->toRfc4122());
        self::assertEquals('018b8dc3-c7a0-f0fe-fff0-fefff0ffffff', $sequence->next()->toRfc4122());
        // overflow
        self::assertEquals('018b8dc3-c7a1-f0fe-fff0-fefff0fffef0', $sequence->next()->toRfc4122());
        self::assertEquals('018b8dc3-c7a1-f0fe-fff0-fefff0fffef1', $sequence->next()->toRfc4122());

        for ($i = 0; $i < 268; $i++) { // roll to another overflow
            $sequence->next();
        }

        self::assertEquals('018b8dc3-c7a1-f0fe-fff0-fefff0fffffe', $sequence->next()->toRfc4122());
        self::assertEquals('018b8dc3-c7a1-f0fe-fff0-fefff0ffffff', $sequence->next()->toRfc4122());
        // overflow
        self::assertEquals('018b8dc3-c7a2-f0fe-fff0-fefff0fffef0', $sequence->next()->toRfc4122());
        self::assertEquals('018b8dc3-c7a2-f0fe-fff0-fefff0fffef1', $sequence->next()->toRfc4122());

        // advance 1msec. the timestamp should still be lower
        $clock->dateTime->modify('+1msec');

        // same clock seq continues
        self::assertEquals('018b8dc3-c7a2-f0fe-fff0-fefff0fffef2', $sequence->next()->toRfc4122());
    }

    public function testOverflowV7Compatible(): void
    {
        $randomizer = new Randomizer(new FixedSequenceEngine("\xf0\xfe\xff"));
        $clock = MutableClock::fromTimestamp(1698890500);

        $sequence = SequenceFactory::ulid(true, $clock, $randomizer);

        self::assertEquals('018b8dc3-c7a0-70fe-bff0-fefff0fffef0', $sequence->next()->toRfc4122());

        for ($i = 0; $i < 269; $i++) { // roll to an overflow
            $sequence->next();
        }

        self::assertEquals('018b8dc3-c7a0-70fe-bff0-fefff0fffffe', $sequence->next()->toRfc4122());
        self::assertEquals('018b8dc3-c7a0-70fe-bff0-fefff0ffffff', $sequence->next()->toRfc4122());
        // overflow
        self::assertEquals('018b8dc3-c7a1-70fe-bff0-fefff0fffef0', $sequence->next()->toRfc4122());
        self::assertEquals('018b8dc3-c7a1-70fe-bff0-fefff0fffef1', $sequence->next()->toRfc4122());

        for ($i = 0; $i < 268; $i++) { // roll to another overflow
            $sequence->next();
        }

        self::assertEquals('018b8dc3-c7a1-70fe-bff0-fefff0fffffe', $sequence->next()->toRfc4122());
        self::assertEquals('018b8dc3-c7a1-70fe-bff0-fefff0ffffff', $sequence->next()->toRfc4122());
        // overflow
        self::assertEquals('018b8dc3-c7a2-70fe-bff0-fefff0fffef0', $sequence->next()->toRfc4122());
        self::assertEquals('018b8dc3-c7a2-70fe-bff0-fefff0fffef1', $sequence->next()->toRfc4122());

        // advance 1msec. the timestamp should still be lower
        $clock->dateTime->modify('+1msec');

        // same clock seq continues
        self::assertEquals('018b8dc3-c7a2-70fe-bff0-fefff0fffef2', $sequence->next()->toRfc4122());
    }

    public function testIterator(): void
    {
        $clock = new StaticClock();

        $seq1 = SequenceFactory::ulid(false, $clock, new Randomizer(new Xoshiro256StarStar(123)));
        $seq2 = SequenceFactory::ulid(false, $clock, new Randomizer(new Xoshiro256StarStar(123)));

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
