<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Clock\TickingClock;
use Arokettu\Uuid\SequenceFactory;
use Arokettu\Uuid\UlidFactory;
use Arokettu\Uuid\UuidFactory;
use PHPUnit\Framework\TestCase;
use Random\Engine\PcgOneseq128XslRr64;
use Random\Randomizer;

class DeprecationsTest extends TestCase
{
    public function testSeqV7(): void
    {
        $clock = TickingClock::fromDateString('+1 sec');
        $randEngine = new PcgOneseq128XslRr64();

        $seq1 = SequenceFactory::v7(clone $clock, new Randomizer(clone $randEngine));
        $seq2 = UuidFactory::v7Sequence(true, clone $clock, new Randomizer(clone $randEngine));
        // reserve has no effect now
        $seq3 = UuidFactory::v7Sequence(false, clone $clock, new Randomizer(clone $randEngine));

        for ($i = 0; $i < 16; $i++) {
            $uuid1 = $seq1->next();
            $uuid2 = $seq2->next();
            $uuid3 = $seq3->next();

            self::assertEquals($uuid1, $uuid2);
            self::assertEquals($uuid1, $uuid3);
        }
    }

    public function testSeqV7Iter(): void
    {
        $clock = TickingClock::fromDateString('+1 sec');
        $randEngine = new PcgOneseq128XslRr64();

        $seq1 = SequenceFactory::v7(clone $clock, new Randomizer(clone $randEngine));
        $seq2 = UuidFactory::v7Sequence(true, clone $clock, new Randomizer(clone $randEngine));

        $i = 0;
        foreach ($seq2 as $uuid2) {
            $uuid1 = $seq1->next();
            self::assertEquals($uuid1, $uuid2);

            $i++;
            if ($i > 16) {
                break;
            }
        }
    }

    public function testSeqUlid(): void
    {
        $clock = TickingClock::fromDateString('+1 sec');
        $randEngine = new PcgOneseq128XslRr64();

        $seq1 = SequenceFactory::ulid(false, clone $clock, new Randomizer(clone $randEngine));
        $seq2 = UlidFactory::sequence(false, true, clone $clock, new Randomizer(clone $randEngine));
        // reserve has no effect now
        $seq3 = UlidFactory::sequence(false, false, clone $clock, new Randomizer(clone $randEngine));

        for ($i = 0; $i < 16; $i++) {
            $ulid1 = $seq1->next();
            $ulid2 = $seq2->next();
            $ulid3 = $seq3->next();

            self::assertEquals($ulid1, $ulid2);
            self::assertEquals($ulid1, $ulid3);
        }
    }

    public function testSeqUlidCompatible(): void
    {
        $clock = TickingClock::fromDateString('+1 sec');
        $randEngine = new PcgOneseq128XslRr64();

        $seq1 = SequenceFactory::ulid(true, clone $clock, new Randomizer(clone $randEngine));
        $seq2 = UlidFactory::sequence(true, true, clone $clock, new Randomizer(clone $randEngine));
        // reserve has no effect now
        $seq3 = UlidFactory::sequence(true, false, clone $clock, new Randomizer(clone $randEngine));

        for ($i = 0; $i < 16; $i++) {
            $ulid1 = $seq1->next();
            $ulid2 = $seq2->next();
            $ulid3 = $seq3->next();

            self::assertEquals($ulid1, $ulid2);
            self::assertEquals($ulid1, $ulid3);
        }
    }

    public function testUlidIter(): void
    {
        $clock = TickingClock::fromDateString('+1 sec');
        $randEngine = new PcgOneseq128XslRr64();

        $seq1 = SequenceFactory::ulid(false, clone $clock, new Randomizer(clone $randEngine));
        $seq2 = UlidFactory::Sequence(false, true, clone $clock, new Randomizer(clone $randEngine));

        $i = 0;
        foreach ($seq2 as $ulid2) {
            $ulid1 = $seq1->next();
            self::assertEquals($ulid1, $ulid2);

            $i++;
            if ($i > 16) {
                break;
            }
        }
    }
}
