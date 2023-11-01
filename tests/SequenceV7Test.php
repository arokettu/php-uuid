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

class SequenceV7Test extends TestCase
{
    public function testSequences(): void
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

        $sequence = SequenceFactory::v7($clock, $randomizer);

        self::assertEquals('02000000-0000-71f9-9e4d-6d65c7e335f8', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-71fa-afa6-f2c3462baa77', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-71fb-8682-cfaa99028220', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-71fc-9e78-9d95b3d87856', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-71fd-aa28-295af8ebf9ff', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-71fe-9b75-f8449b23c260', $sequence->next()->toRfc4122());
    }

    public function testProperRandomizerWithAdvance(): void
    {
        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = new MutableClock(new \DateTime('2039-09-07 15:47:35.552'));

        $sequence = SequenceFactory::v7($clock, $randomizer);

        self::assertEquals('02000000-0000-71f9-9e4d-6d65c7e335f8', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-71fa-afa6-f2c3462baa77', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-71fb-8682-cfaa99028220', $sequence->next()->toRfc4122());

        $clock->dateTime->modify('+1 second');

        self::assertEquals('02000000-03e8-70de-aa28-295af8ebf9ff', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-03e8-70df-9b75-f8449b23c260', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-03e8-70e0-951a-7e9d570a1aa8', $sequence->next()->toRfc4122());
    }

    public function testProperRandomizerWithAdvanceEveryStep(): void
    {
        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = new TickingClock(
            \DateInterval::createFromDateString('+128 msec'),
            new \DateTime('2039-09-07 15:47:35.552'),
        );

        $sequence = SequenceFactory::v7($clock, $randomizer);

        self::assertEquals('02000000-0000-71f9-9e4d-6d65c7e335f8', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0080-762f-8682-cfaa99028220', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0100-70de-aa28-295af8ebf9ff', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0180-751b-951a-7e9d570a1aa8', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0200-77d4-b05c-234f8095766f', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0280-77ba-8d48-f3844e4600c4', $sequence->next()->toRfc4122());
    }

    public function testOverflowWorstCase(): void
    {
        $randomizer = new Randomizer(new FixedSequenceEngine("\xff"));

        $sequence = SequenceFactory::v7(new StaticClock(), $randomizer);
        $sequence->next();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Counter sequence overflow');
        $sequence->next();
    }

    public function testOverflowGuaranteedWorstCase(): void
    {
        $randomizer = new Randomizer(new FixedSequenceEngine("\xff"));

        $sequence = SequenceFactory::v7(new StaticClock(), $randomizer);

        for ($i = 0; $i < 2049; $i++) { // 0x07ff - 0x0fff inclusive
            $sequence->next();
        }

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Counter sequence overflow');
        var_dump($sequence->next()->toRfc4122());
    }

    public function testOverflowBestCase(): void
    {
        $randomizer = new Randomizer(new FixedSequenceEngine("\x00"));

        $sequence = SequenceFactory::v7(new StaticClock(), $randomizer);

        for ($i = 0; $i < 4096; $i++) {
            $sequence->next();
        }

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Counter sequence overflow');
        var_dump($sequence->next()->toRfc4122());
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
}
