<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Clock\MutableClock;
use Arokettu\Clock\StaticClock;
use Arokettu\Clock\TickingClock;
use Arokettu\Uuid\ClockSequences\ClockSequence;
use Arokettu\Uuid\Nodes\RandomNode;
use Arokettu\Uuid\Nodes\StaticNode;
use Arokettu\Uuid\SequenceFactory;
use PHPUnit\Framework\TestCase;
use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

class SequenceV1Test extends TestCase
{
    public function testSequenceStaticTime(): void
    {
        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = StaticClock::fromDateString('2020-01-01 23:01:23.456789');
        $node = StaticNode::random($randomizer);

        $sequence = SequenceFactory::v1($node, null, $clock, $randomizer);

        self::assertEquals('a1ab96d2-2cea-11ea-8d5e-f969a0d1a18f', $sequence->next()->toRfc4122());
        self::assertEquals('a1ab96d3-2cea-11ea-8d5e-f969a0d1a18f', $sequence->next()->toRfc4122());
        self::assertEquals('a1ab96d4-2cea-11ea-8d5e-f969a0d1a18f', $sequence->next()->toRfc4122());
        self::assertEquals('a1ab96d5-2cea-11ea-8d5e-f969a0d1a18f', $sequence->next()->toRfc4122());
    }

    public function testSequenceTickingTime(): void
    {
        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = TickingClock::fromDateString('+128 usec', '2020-01-01 23:01:23.456789');
        $node = StaticNode::random($randomizer);

        $sequence = SequenceFactory::v1($node, null, $clock, $randomizer);

        self::assertEquals('a1ab96d2-2cea-11ea-8d5e-f969a0d1a18f', $sequence->next()->toRfc4122());
        self::assertEquals('a1ab9bd2-2cea-11ea-8d5e-f969a0d1a18f', $sequence->next()->toRfc4122());
        self::assertEquals('a1aba0d2-2cea-11ea-8d5e-f969a0d1a18f', $sequence->next()->toRfc4122());
        self::assertEquals('a1aba5d2-2cea-11ea-8d5e-f969a0d1a18f', $sequence->next()->toRfc4122());
    }

    public function testOverflow(): void
    {
        // 2000_0000_0000_008, next are 2000_0000_0000_012, 2000_0000_0000_01c
        $clock = MutableClock::fromDateString('2039-06-20 23:40:07.585588');
        $node = StaticNode::fromHex('999999999999');

        $sequence = SequenceFactory::v1($node, 0x123, $clock);

        self::assertEquals('00000008-0000-1200-8123-999999999999', $sequence->next()->toRfc4122());

        for ($i = 0; $i < 7; $i++) { // skip to a counter overflow
            $sequence->next();
        }

        self::assertEquals('00000010-0000-1200-8123-999999999999', $sequence->next()->toRfc4122());
        self::assertEquals('00000011-0000-1200-8123-999999999999', $sequence->next()->toRfc4122());
        // counter overflow
        self::assertEquals('00000012-0000-1200-8123-999999999999', $sequence->next()->toRfc4122());
        self::assertEquals('00000013-0000-1200-8123-999999999999', $sequence->next()->toRfc4122());

        for ($i = 0; $i < 6; $i++) { // skip to a time overflow
            $sequence->next();
        }

        // dt to check
        $dt1 = new \DateTimeImmutable('2039-06-20T23:40:07.585589'); // + 1 usec // first overflow
        $dt2 = new \DateTimeImmutable('2039-06-20 23:40:07.585590'); // + 2 usec // second overflow

        self::assertEquals('0000001a-0000-1200-8123-999999999999', $sequence->next()->toRfc4122());
        self::assertEquals('0000001b-0000-1200-8123-999999999999', ($uuid1 = $sequence->next())->toRfc4122());
        // overflow
        self::assertEquals('0000001c-0000-1200-8123-999999999999', ($uuid2 = $sequence->next())->toRfc4122());
        self::assertEquals('0000001d-0000-1200-8123-999999999999', $sequence->next()->toRfc4122());

        self::assertEquals($dt1, $uuid1->getDateTime());
        self::assertEquals($dt2, $uuid2->getDateTime());

        for ($i = 0; $i < 6; $i++) { // skip to somewhere after a next time overflow
            $sequence->next();
        }

        self::assertEquals('00000024-0000-1200-8123-999999999999', $sequence->next()->toRfc4122());
        self::assertEquals('00000025-0000-1200-8123-999999999999', $sequence->next()->toRfc4122());

        $clock->dateTime->modify('+1 usec'); // advancing time should not change the counters

        self::assertEquals('00000026-0000-1200-8123-999999999999', $sequence->next()->toRfc4122());
        self::assertEquals('00000027-0000-1200-8123-999999999999', $sequence->next()->toRfc4122());

        $clock->dateTime->modify('+2 usec'); // catching up

        self::assertEquals('00000028-0000-1200-8123-999999999999', $sequence->next()->toRfc4122());
        self::assertEquals('00000029-0000-1200-8123-999999999999', $sequence->next()->toRfc4122());

        $clock->dateTime->modify('+2 usec'); // advancing beyond, regenerate the counter

        self::assertEquals('0000003a-0000-1200-8123-999999999999', $sequence->next()->toRfc4122());
        self::assertEquals('0000003b-0000-1200-8123-999999999999', $sequence->next()->toRfc4122());
    }

    public function testWithVariableAdvance(): void
    {
        $randomizer = new Randomizer(new Xoshiro256StarStar(123));
        $clock = new MutableClock(new \DateTime('2039-06-20 23:40:07.585588'));
        $node = new RandomNode($randomizer);

        $sequence = SequenceFactory::v1($node, ClockSequence::Random, $clock, $randomizer);

        self::assertEquals('00000008-0000-1200-a9f9-5f4d6d65c7e3', $sequence->next()->toRfc4122());
        self::assertEquals('00000009-0000-1200-a62f-0782cfaa9902', $sequence->next()->toRfc4122());
        self::assertEquals('0000000a-0000-1200-b8de-6b28295af8eb', $sequence->next()->toRfc4122());

        $clock->dateTime->modify('+1 second');

        self::assertEquals('00989688-0000-1200-b51b-551a7e9d570a', $sequence->next()->toRfc4122());
        self::assertEquals('00989689-0000-1200-9fd4-715c234f8095', $sequence->next()->toRfc4122());
        self::assertEquals('0098968a-0000-1200-b7ba-4d48f3844e46', $sequence->next()->toRfc4122());

        // if clock goes back, ignore the timestamp
        $clock->dateTime->modify('-5 seconds');

        self::assertEquals('0098968b-0000-1200-aac5-f53429e798cd', $sequence->next()->toRfc4122());
        self::assertEquals('0098968c-0000-1200-8c70-13b33da29b3d', $sequence->next()->toRfc4122());
        self::assertEquals('0098968d-0000-1200-8534-27454ba0df56', $sequence->next()->toRfc4122());

        // when time catches up, use it
        $clock->dateTime->modify('+10 seconds');

        self::assertEquals('03938708-0000-1200-abef-4f6663a8e581', $sequence->next()->toRfc4122());
        self::assertEquals('03938709-0000-1200-b857-13911b35e0dd', $sequence->next()->toRfc4122());
        self::assertEquals('0393870a-0000-1200-bdc9-87ab8139ae1b', $sequence->next()->toRfc4122());

        // no rounding test because ts for v1/v6 is more precise than in current PHP
    }

    public function testIterator(): void
    {
        $clock = new StaticClock();

        $seq1 = SequenceFactory::v1(clock: $clock, randomizer: new Randomizer(new Xoshiro256StarStar(123)));
        $seq2 = SequenceFactory::v1(clock: $clock, randomizer: new Randomizer(new Xoshiro256StarStar(123)));

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
