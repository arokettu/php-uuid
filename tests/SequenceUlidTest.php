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
                '01963972-0580-9999-9999-999999999999', '01963972-0580-9999-9999-99999a3333cc',
                '01963972-0580-9999-9999-99999acccdff', '01963972-0580-9999-9999-99999b666832',
                '01963972-0580-9999-9999-99999c000265', '01963972-0580-9999-9999-99999c999c98',
                '01963972-0580-9999-9999-99999d3336cb', '01963972-0580-9999-9999-99999dccd0fe',
                // skip
                '01963972-0968-9999-9999-999999999999', '01963972-0968-9999-9999-99999a3333cc',
                '01963972-0968-9999-9999-99999acccdff', '01963972-0968-9999-9999-99999b666832',
                '01963972-0968-9999-9999-99999c000265', '01963972-0968-9999-9999-99999c999c98',
                '01963972-0968-9999-9999-99999d3336cb', '01963972-0968-9999-9999-99999dccd0fe',
            ]],
            ['num' => 8, 'v7' => true, 'uuids' => [
                '01963972-0580-7999-9999-999999999999', '01963972-0580-7999-9999-99999a3333cc',
                '01963972-0580-7999-9999-99999acccdff', '01963972-0580-7999-9999-99999b666832',
                '01963972-0580-7999-9999-99999c000265', '01963972-0580-7999-9999-99999c999c98',
                '01963972-0580-7999-9999-99999d3336cb', '01963972-0580-7999-9999-99999dccd0fe',
                // skip
                '01963972-0968-7999-9999-999999999999', '01963972-0968-7999-9999-99999a3333cc',
                '01963972-0968-7999-9999-99999acccdff', '01963972-0968-7999-9999-99999b666832',
                '01963972-0968-7999-9999-99999c000265', '01963972-0968-7999-9999-99999c999c98',
                '01963972-0968-7999-9999-99999d3336cb', '01963972-0968-7999-9999-99999dccd0fe',
            ]],
        ];

        foreach ($sequences as ['num' => $num, 'v7' => $v7, 'uuids' => $uuids]) {
            $clock = new MutableClock(new \DateTime('2025-04-15 12:34:56'));
            $randomizer = new Randomizer(new FixedSequenceEngine("\x99")); // +0x999a33 every step

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

        self::assertEquals('02000000-0000-7969-a0d1-6d4d5ef2a62f', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7969-a0d1-6d4d5fc228e0', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7969-a0d1-6d4d605fa254', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7969-a0d1-6d4d6088cb19', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7969-a0d1-6d4d61814079', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7969-a0d1-6d4d61ff5b6c', $sequence->next()->toRfc4122());

        $randomizer = new Randomizer(new Xoshiro256StarStar(123));

        $sequence = SequenceFactory::ulid(false, $clock, $randomizer);

        self::assertEquals('02000000-0000-f969-a0d1-6d4d5ef2a62f', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-f969-a0d1-6d4d5fc228e0', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-f969-a0d1-6d4d605fa254', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-f969-a0d1-6d4d6088cb19', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-f969-a0d1-6d4d61814079', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-f969-a0d1-6d4d61ff5b6c', $sequence->next()->toRfc4122());
    }

    public function testProperRandomizerWithAdvance(): void
    {
        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = new MutableClock(new \DateTime('2039-09-07 15:47:35.552'));

        $sequence = SequenceFactory::ulid(false, $clock, $randomizer);

        self::assertEquals('02000000-0000-f969-a0d1-6d4d5ef2a62f', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-f969-a0d1-6d4d5fc228e0', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-f969-a0d1-6d4d605fa254', $sequence->next()->toRfc4122());

        $clock->dateTime->modify('+1 second');

        self::assertEquals('02000000-03e8-6a28-295a-f8751b7e1a55', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-03e8-6a28-295a-f8751bdafa97', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-03e8-6a28-295a-f8751bfe5757', $sequence->next()->toRfc4122());

        // if clock goes back, ignore the timestamp
        $clock->dateTime->modify('-5 seconds');

        self::assertEquals('02000000-03e8-6a28-295a-f8751c4c8fba', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-03e8-6a28-295a-f8751d3fd88c', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-03e8-6a28-295a-f8751e3f03e3', $sequence->next()->toRfc4122());

        // when time catches up, use it
        $clock->dateTime->modify('+10 seconds');

        self::assertEquals('02000000-1770-f434-29e7-ae4c703db313', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-1770-f434-29e7-ae4c7065b887', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-1770-f434-29e7-ae4c70b0fe4e', $sequence->next()->toRfc4122());

        // the change below 1msec should not disrupt the counter
        $clock->dateTime->modify('+500 usec');

        self::assertEquals('02000000-1770-f434-29e7-ae4c717eeacc', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-1770-f434-29e7-ae4c71e251c4', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-1770-f434-29e7-ae4c728a8a5c', $sequence->next()->toRfc4122());
    }

    public function testProperRandomizerWithAdvanceV7Compatible(): void
    {
        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = new MutableClock(new \DateTime('2039-09-07 15:47:35.552'));

        $sequence = SequenceFactory::ulid(true, $clock, $randomizer);

        self::assertEquals('02000000-0000-7969-a0d1-6d4d5ef2a62f', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7969-a0d1-6d4d5fc228e0', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-7969-a0d1-6d4d605fa254', $sequence->next()->toRfc4122());

        $clock->dateTime->modify('+1 second');

        self::assertEquals('02000000-03e8-7a28-a95a-f8751b7e1a55', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-03e8-7a28-a95a-f8751bdafa97', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-03e8-7a28-a95a-f8751bfe5757', $sequence->next()->toRfc4122());

        // if clock goes back, ignore the timestamp
        $clock->dateTime->modify('-5 seconds');

        self::assertEquals('02000000-03e8-7a28-a95a-f8751c4c8fba', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-03e8-7a28-a95a-f8751d3fd88c', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-03e8-7a28-a95a-f8751e3f03e3', $sequence->next()->toRfc4122());

        // when time catches up, use it
        $clock->dateTime->modify('+10 seconds');

        self::assertEquals('02000000-1770-7434-a9e7-ae4c703db313', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-1770-7434-a9e7-ae4c7065b887', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-1770-7434-a9e7-ae4c70b0fe4e', $sequence->next()->toRfc4122());

        // the change below 1msec should not disrupt the counter
        $clock->dateTime->modify('+500 usec');

        self::assertEquals('02000000-1770-7434-a9e7-ae4c717eeacc', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-1770-7434-a9e7-ae4c71e251c4', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-1770-7434-a9e7-ae4c728a8a5c', $sequence->next()->toRfc4122());
    }

    public function testProperRandomizerWithAdvanceEveryStep(): void
    {
        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = TickingClock::fromDateString('+128 msec', '2039-09-07 15:47:35.552');

        $sequence = SequenceFactory::ulid(false, $clock, $randomizer);

        self::assertEquals('02000000-0000-f969-a0d1-6d4d5ef2a62f', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0080-0682-cfaa-9d78de29286a', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0100-1b75-f844-7e1a555cdfd4', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0180-705c-234f-4e37baf3484d', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0200-c52a-ff91-2934f4ae4c70', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0280-13b3-3da2-2805344b4526', $sequence->next()->toRfc4122());

        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = TickingClock::fromDateString('+128 msec', '2039-09-07 15:47:35.552');

        $sequence = SequenceFactory::ulid(true, $clock, $randomizer);

        self::assertEquals('02000000-0000-7969-a0d1-6d4d5ef2a62f', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0080-7682-8faa-9d78de29286a', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0100-7b75-b844-7e1a555cdfd4', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0180-705c-a34f-4e37baf3484d', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0200-752a-bf91-2934f4ae4c70', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0280-73b3-bda2-2805344b4526', $sequence->next()->toRfc4122());
    }

    public function testOverflow(): void
    {
        $randomizer = new Randomizer(new FixedSequenceEngine("\xfc\xff\xff")); // ~12297 ids/msec
        $clock = MutableClock::fromTimestamp(1698890500);

        $sequence = SequenceFactory::ulid(false, $clock, $randomizer);

        self::assertEquals('018b8dc3-c7a0-fcff-fffc-fffffcfffffc', $sequence->next()->toRfc4122());

        for ($i = 0; $i < 12295; $i++) { // roll to an overflow
            $sequence->next();
        }

        self::assertEquals('018b8dc3-c7a0-fcff-fffc-ffffffffefe4', $sequence->next()->toRfc4122());
        self::assertEquals('018b8dc3-c7a0-fcff-fffc-ffffffffffe1', $sequence->next()->toRfc4122());
        // overflow
        self::assertEquals('018b8dc3-c7a1-fcff-fffc-fffffcfffffc', $sequence->next()->toRfc4122());
        self::assertEquals('018b8dc3-c7a1-fcff-fffc-fffffd000ff9', $sequence->next()->toRfc4122());

        for ($i = 0; $i < 12294; $i++) { // roll to another overflow
            $sequence->next();
        }

        self::assertEquals('018b8dc3-c7a1-fcff-fffc-ffffffffefe4', $sequence->next()->toRfc4122());
        self::assertEquals('018b8dc3-c7a1-fcff-fffc-ffffffffffe1', $sequence->next()->toRfc4122());
        // overflow
        self::assertEquals('018b8dc3-c7a2-fcff-fffc-fffffcfffffc', $sequence->next()->toRfc4122());
        self::assertEquals('018b8dc3-c7a2-fcff-fffc-fffffd000ff9', $sequence->next()->toRfc4122());

        // advance 1msec. the timestamp should still be lower
        $clock->dateTime->modify('+1msec');

        // same clock seq continues
        self::assertEquals('018b8dc3-c7a2-fcff-fffc-fffffd001ff6', $sequence->next()->toRfc4122());
    }

    public function testOverflowV7Compatible(): void
    {
        $randomizer = new Randomizer(new FixedSequenceEngine("\xfc\xff\xff")); // ~12297 ids/msec
        $clock = MutableClock::fromTimestamp(1698890500);

        $sequence = SequenceFactory::ulid(true, $clock, $randomizer);

        self::assertEquals('018b8dc3-c7a0-7cff-bffc-fffffcfffffc', $sequence->next()->toRfc4122());

        for ($i = 0; $i < 12295; $i++) { // roll to an overflow
            $sequence->next();
        }

        self::assertEquals('018b8dc3-c7a0-7cff-bffc-ffffffffefe4', $sequence->next()->toRfc4122());
        self::assertEquals('018b8dc3-c7a0-7cff-bffc-ffffffffffe1', $sequence->next()->toRfc4122());
        // overflow
        self::assertEquals('018b8dc3-c7a1-7cff-bffc-fffffcfffffc', $sequence->next()->toRfc4122());
        self::assertEquals('018b8dc3-c7a1-7cff-bffc-fffffd000ff9', $sequence->next()->toRfc4122());

        for ($i = 0; $i < 12294; $i++) { // roll to another overflow
            $sequence->next();
        }

        self::assertEquals('018b8dc3-c7a1-7cff-bffc-ffffffffefe4', $sequence->next()->toRfc4122());
        self::assertEquals('018b8dc3-c7a1-7cff-bffc-ffffffffffe1', $sequence->next()->toRfc4122());
        // overflow
        self::assertEquals('018b8dc3-c7a2-7cff-bffc-fffffcfffffc', $sequence->next()->toRfc4122());
        self::assertEquals('018b8dc3-c7a2-7cff-bffc-fffffd000ff9', $sequence->next()->toRfc4122());

        // advance 1msec. the timestamp should still be lower
        $clock->dateTime->modify('+1msec');

        // same clock seq continues
        self::assertEquals('018b8dc3-c7a2-7cff-bffc-fffffd001ff6', $sequence->next()->toRfc4122());
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

    public function testNoZeroIncrement(): void
    {
        // since ulid is the only sequence with random increment, test that it is never zero
        $clock = StaticClock::fromTimestamp(1701394201);
        $randomizer = new Randomizer(new FixedSequenceEngine("\0"));

        $seq = SequenceFactory::ulid(false, $clock, $randomizer);

        self::assertEquals('01HGHFYED80000000000000000', $seq->next()->toString());
        self::assertEquals('01HGHFYED80000000000000001', $seq->next()->toString()); // +1
        self::assertEquals('01HGHFYED80000000000000002', $seq->next()->toString()); // +1
        self::assertEquals('01HGHFYED80000000000000003', $seq->next()->toString()); // +1
    }
}
