<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Clock\MutableClock;
use Arokettu\Clock\StaticClock;
use Arokettu\Clock\TickingClock;
use Arokettu\Uuid\SequenceFactory;
use Arokettu\Uuid\Tests\Helper\FixedSequenceEngine;
use PHPUnit\Framework\TestCase;
use Random\Engine\PcgOneseq128XslRr64;
use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

final class SequenceV7Test extends TestCase
{
    public function testShortSequence(): void
    {
        $sequences = [
            ['num' => 8, 'uuids' => [
                '01963972-0580-7199-9999-999999999999', '01963972-0580-719a-9999-999999999999',
                '01963972-0580-719b-9999-999999999999', '01963972-0580-719c-9999-999999999999',
                '01963972-0580-719d-9999-999999999999', '01963972-0580-719e-9999-999999999999',
                '01963972-0580-719f-9999-999999999999', '01963972-0580-71a0-9999-999999999999',
                // skip
                '01963972-0968-7199-9999-999999999999', '01963972-0968-719a-9999-999999999999',
                '01963972-0968-719b-9999-999999999999', '01963972-0968-719c-9999-999999999999',
                '01963972-0968-719d-9999-999999999999', '01963972-0968-719e-9999-999999999999',
                '01963972-0968-719f-9999-999999999999', '01963972-0968-71a0-9999-999999999999',
            ]],
        ];

        foreach ($sequences as ['num' => $num, 'uuids' => $uuids]) {
            $clock = new MutableClock(new \DateTime('2025-04-15 12:34:56'));
            $randomizer = new Randomizer(new FixedSequenceEngine("\x99"));

            $seq = SequenceFactory::v7($clock, $randomizer);

            for ($i = 0; $i < $num; $i++) {
                self::assertEquals($uuids[$i], $seq->next()->toRfcFormat(), "i: $i");
            }

            $clock->dateTime->modify('+1 sec');

            for ($i = 0; $i < $num; $i++) {
                self::assertEquals($uuids[$i + $num], $seq->next()->toRfcFormat(), "i2: $i");
            }
        }
    }

    public function testLongSequence(): void
    {
        // long sequence is the same code as ULID so don't test its edge cases, only the basic generation
        $sequences = [
            ['num' => 8, 'uuids' => [
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

        foreach ($sequences as ['num' => $num, 'uuids' => $uuids]) {
            $clock = new MutableClock(new \DateTime('2025-04-15 12:34:56'));
            $randomizer = new Randomizer(new FixedSequenceEngine("\x99")); // +0x999a33 every step

            $seq = SequenceFactory::v7Long($clock, $randomizer);

            for ($i = 0; $i < $num; $i++) {
                self::assertEquals($uuids[$i], $seq->next()->toRfcFormat(), "i: $i");
            }

            $clock->dateTime->modify('+1 sec');

            for ($i = 0; $i < $num; $i++) {
                self::assertEquals($uuids[$i + $num], $seq->next()->toRfcFormat(), "i2: $i");
            }
        }
    }

    public function testProperRandomizer(): void
    {
        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = new StaticClock(new \DateTime('2039-09-07 15:47:35.552'));

        $sequence = SequenceFactory::v7($clock, $randomizer);

        self::assertEquals('02000000-0000-71f9-9e4d-6d65c7e335f8', $sequence->next()->toRfcFormat());
        self::assertEquals('02000000-0000-71fa-afa6-f2c3462baa77', $sequence->next()->toRfcFormat());
        self::assertEquals('02000000-0000-71fb-8682-cfaa99028220', $sequence->next()->toRfcFormat());
        self::assertEquals('02000000-0000-71fc-9e78-9d95b3d87856', $sequence->next()->toRfcFormat());
        self::assertEquals('02000000-0000-71fd-aa28-295af8ebf9ff', $sequence->next()->toRfcFormat());
        self::assertEquals('02000000-0000-71fe-9b75-f8449b23c260', $sequence->next()->toRfcFormat());
    }

    public function testProperRandomizerWithAdvance(): void
    {
        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = new MutableClock(new \DateTime('2039-09-07 15:47:35.552'));

        $sequence = SequenceFactory::v7($clock, $randomizer);

        self::assertEquals('02000000-0000-71f9-9e4d-6d65c7e335f8', $sequence->next()->toRfcFormat());
        self::assertEquals('02000000-0000-71fa-afa6-f2c3462baa77', $sequence->next()->toRfcFormat());
        self::assertEquals('02000000-0000-71fb-8682-cfaa99028220', $sequence->next()->toRfcFormat());

        $clock->dateTime->modify('+1 second');

        self::assertEquals('02000000-03e8-70de-aa28-295af8ebf9ff', $sequence->next()->toRfcFormat());
        self::assertEquals('02000000-03e8-70df-9b75-f8449b23c260', $sequence->next()->toRfcFormat());
        self::assertEquals('02000000-03e8-70e0-951a-7e9d570a1aa8', $sequence->next()->toRfcFormat());

        // if clock goes back, ignore the timestamp
        $clock->dateTime->modify('-5 seconds');

        self::assertEquals('02000000-03e8-70e1-94df-5c6daf02d3c2', $sequence->next()->toRfcFormat());
        self::assertEquals('02000000-03e8-70e2-b05c-234f8095766f', $sequence->next()->toRfcFormat());
        self::assertEquals('02000000-03e8-70e3-ba37-4ea83797f7a6', $sequence->next()->toRfcFormat());

        // when time catches up, use it
        $clock->dateTime->modify('+10 seconds');

        self::assertEquals('02000000-1770-704d-852a-ff9189fcae09', $sequence->next()->toRfcFormat());
        self::assertEquals('02000000-1770-704e-b434-29e798cd8c51', $sequence->next()->toRfcFormat());
        self::assertEquals('02000000-1770-704f-b04c-ae215dcb0ca9', $sequence->next()->toRfcFormat());

        // the change below 1msec should not disrupt the counter
        $clock->dateTime->modify('+500 usec');

        self::assertEquals('02000000-1770-7050-93b3-3da29b3d812f', $sequence->next()->toRfcFormat());
        self::assertEquals('02000000-1770-7051-b405-283f75a98a52', $sequence->next()->toRfcFormat());
        self::assertEquals('02000000-1770-7052-a645-4ba0df565fbc', $sequence->next()->toRfcFormat());
    }

    public function testProperRandomizerWithAdvanceEveryStep(): void
    {
        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = new TickingClock(
            \DateInterval::createFromDateString('+128 msec'),
            new \DateTime('2039-09-07 15:47:35.552'),
        );

        $sequence = SequenceFactory::v7($clock, $randomizer);

        self::assertEquals('02000000-0000-71f9-9e4d-6d65c7e335f8', $sequence->next()->toRfcFormat());
        self::assertEquals('02000000-0080-762f-8682-cfaa99028220', $sequence->next()->toRfcFormat());
        self::assertEquals('02000000-0100-70de-aa28-295af8ebf9ff', $sequence->next()->toRfcFormat());
        self::assertEquals('02000000-0180-751b-951a-7e9d570a1aa8', $sequence->next()->toRfcFormat());
        self::assertEquals('02000000-0200-77d4-b05c-234f8095766f', $sequence->next()->toRfcFormat());
        self::assertEquals('02000000-0280-77ba-8d48-f3844e4600c4', $sequence->next()->toRfcFormat());
    }

    public function testOverflow(): void
    {
        $randomizer = new Randomizer(new PcgOneseq128XslRr64(123));
        $clock = MutableClock::fromTimestamp(1698888001);

        $sequence = SequenceFactory::v7($clock, $randomizer);

        self::assertEquals('018b8d9d-a5e8-773d-9282-2ff70f52cfc7', $sequence->next()->toString());

        for ($i = 0; $i < 2240; $i++) { // roll to an overflow
            $sequence->next()->toString();
        }

        self::assertEquals('018b8d9d-a5e8-7ffe-9bae-91c66e6911d5', $sequence->next()->toString());
        self::assertEquals('018b8d9d-a5e8-7fff-921a-a26d89246907', $sequence->next()->toString());
        // overflow
        self::assertEquals('018b8d9d-a5e9-7535-a74c-22026bf76954', $sequence->next()->toString());
        self::assertEquals('018b8d9d-a5e9-7536-aedb-8d836a766a26', $sequence->next()->toString());

        for ($i = 0; $i < 2759; $i++) { // roll to another overflow
            $sequence->next()->toString();
        }

        self::assertEquals('018b8d9d-a5e9-7ffe-b0af-2e8c3dfa42c8', $sequence->next()->toString());
        self::assertEquals('018b8d9d-a5e9-7fff-9a19-53e11bf3c1d8', $sequence->next()->toString());
        // another overflow
        self::assertEquals('018b8d9d-a5ea-70bb-9575-2c28cc59476d', $sequence->next()->toString());
        self::assertEquals('018b8d9d-a5ea-70bc-906c-81640f3d9553', $sequence->next()->toString());

        // advance 1msec. the timestamp should still be lower
        $clock->dateTime->modify('+1msec');

        // same clock seq continues
        self::assertEquals('018b8d9d-a5ea-70bd-a219-8fb6203408f4', $sequence->next()->toString());
    }

    public function testIterator(): void
    {
        $clock = new StaticClock();

        $seq1 = SequenceFactory::v7($clock, new Randomizer(new Xoshiro256StarStar(123)));
        $seq2 = SequenceFactory::v7($clock, new Randomizer(new Xoshiro256StarStar(123)));

        $counter = 10;
        foreach ($seq1 as $uuid) {
            self::assertEquals($seq2->next(), $uuid);
            $counter--;
            if (!$counter) {
                break;
            }
        }
    }

    public function testLongIterator(): void
    {
        $clock = new StaticClock();

        $seq1 = SequenceFactory::v7Long($clock, new Randomizer(new Xoshiro256StarStar(123)));
        $seq2 = SequenceFactory::v7Long($clock, new Randomizer(new Xoshiro256StarStar(123)));

        $counter = 10;
        foreach ($seq1 as $uuid) {
            self::assertEquals($seq2->next(), $uuid);
            $counter--;
            if (!$counter) {
                break;
            }
        }
    }
}
