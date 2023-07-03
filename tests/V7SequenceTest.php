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
            ['num' => 8, 'reserve' => true, 'uuids' => [
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
            ['num' => 8, 'reserve' => false, 'uuids' => [
                '01963972-0580-7999-9999-999999999999', '01963972-0580-799a-9999-999999999999',
                '01963972-0580-799b-9999-999999999999', '01963972-0580-799c-9999-999999999999',
                '01963972-0580-799d-9999-999999999999', '01963972-0580-799e-9999-999999999999',
                '01963972-0580-799f-9999-999999999999', '01963972-0580-79a0-9999-999999999999',
                // skip
                '01963972-0968-7999-9999-999999999999', '01963972-0968-799a-9999-999999999999',
                '01963972-0968-799b-9999-999999999999', '01963972-0968-799c-9999-999999999999',
                '01963972-0968-799d-9999-999999999999', '01963972-0968-799e-9999-999999999999',
                '01963972-0968-799f-9999-999999999999', '01963972-0968-79a0-9999-999999999999',
            ]],
        ];

        foreach ($sequences as ['num' => $num, 'reserve' => $reserve, 'uuids' => $uuids]) {
            $clock = new MutableClock(new \DateTime('2025-04-15 12:34:56'));
            $randomizer = new Randomizer(new FixedSequenceEngine("\x99"));

            $seq = UuidFactory::v7Sequence($reserve, $randomizer, $clock);

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

        $sequence = UuidFactory::v7Sequence(true, $randomizer, $clock);

        self::assertEquals('02000000-0000-7169-9e4d-6d65c7e335f8', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-716a-afa6-f2c3462baa77', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-716b-8682-cfaa99028220', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-716c-9e78-9d95b3d87856', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-716d-aa28-295af8ebf9ff', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-716e-9b75-f8449b23c260', $sequence->next()->toRfc4122());
    }

    public function testProperRandomizerWithAdvance(): void
    {
        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = new MutableClock(new \DateTime('2039-09-07 15:47:35.552'));

        $sequence = UuidFactory::v7Sequence(true, $randomizer, $clock);

        self::assertEquals('02000000-0000-7169-9e4d-6d65c7e335f8', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-716a-afa6-f2c3462baa77', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0000-716b-8682-cfaa99028220', $sequence->next()->toRfc4122());

        $clock->dateTime->modify('+1 second');

        self::assertEquals('02000000-03e8-7678-aa28-295af8ebf9ff', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-03e8-7679-9b75-f8449b23c260', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-03e8-767a-951a-7e9d570a1aa8', $sequence->next()->toRfc4122());
    }

    public function testProperRandomizerWithAdvanceEveryStep(): void
    {
        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = new TickingClock(
            \DateInterval::createFromDateString('+128 msec'),
            new \DateTime('2039-09-07 15:47:35.552'),
        );

        $sequence = UuidFactory::v7Sequence(true, $randomizer, $clock);

        self::assertEquals('02000000-0000-7169-9e4d-6d65c7e335f8', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0080-77a6-8682-cfaa99028220', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0100-7678-aa28-295af8ebf9ff', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0180-7375-951a-7e9d570a1aa8', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0200-74df-b05c-234f8095766f', $sequence->next()->toRfc4122());
        self::assertEquals('02000000-0280-7237-8d48-f3844e4600c4', $sequence->next()->toRfc4122());
    }

    public function testOverflowWorstCase(): void
    {
        $randomizer = new Randomizer(new FixedSequenceEngine("\xff"));

        $sequence = UuidFactory::v7Sequence(false, $randomizer, new StaticClock());
        $sequence->next();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Counter sequence overflow');
        $sequence->next();
    }

    public function testOverflowGuaranteedWorstCase(): void
    {
        $randomizer = new Randomizer(new FixedSequenceEngine("\xff"));

        $sequence = UuidFactory::v7Sequence(true, $randomizer, new StaticClock());

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

        $sequence = UuidFactory::v7Sequence(false, $randomizer, new StaticClock());

        for ($i = 0; $i < 4096; $i++) {
            $sequence->next();
        }

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Counter sequence overflow');
        var_dump($sequence->next()->toRfc4122());
    }
}
