<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\SequenceFactory;
use Arokettu\Uuid\Tests\Helper\FixedSequenceEngine;
use PHPUnit\Framework\TestCase;
use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

class SequenceV4Test extends TestCase
{
    public function testMin(): void
    {
        $seq = SequenceFactory::v4(new Randomizer(new FixedSequenceEngine("\0")));

        self::assertEquals('00000000-0000-4000-8000-000000000000', $seq->next()->toRfc4122());
        self::assertEquals('00000000-0000-4000-8000-000000000000', $seq->next()->toRfc4122());
    }

    public function testMax(): void
    {
        $seq = SequenceFactory::v4(new Randomizer(new FixedSequenceEngine("\xff")));

        self::assertEquals('ffffffff-ffff-4fff-bfff-ffffffffffff', $seq->next()->toRfc4122());
        self::assertEquals('ffffffff-ffff-4fff-bfff-ffffffffffff', $seq->next()->toRfc4122());
    }

    public function testRandom(): void
    {
        // f969a0d1a18f5a32 5e4d6d65c7e335f8 2fa6f2c3462baa77 0682cfaa99028220
        $seq = SequenceFactory::v4(new Randomizer(new Xoshiro256StarStar(123)));

        self::assertEquals('f969a0d1-a18f-4a32-9e4d-6d65c7e335f8', $seq->next()->toRfc4122());
        self::assertEquals('2fa6f2c3-462b-4a77-8682-cfaa99028220', $seq->next()->toRfc4122());
    }

    public function testIterator(): void
    {
        // f969a0d1a18f5a32 5e4d6d65c7e335f8 2fa6f2c3462baa77 0682cfaa99028220 ...
        $seq = SequenceFactory::v4(new Randomizer(new Xoshiro256StarStar(123)));

        $uuids = [
            'f969a0d1-a18f-4a32-9e4d-6d65c7e335f8',
            '2fa6f2c3-462b-4a77-8682-cfaa99028220',
            'de789d95-b3d8-4856-aa28-295af8ebf9ff',
            '1b75f844-9b23-4260-951a-7e9d570a1aa8',
        ];

        $i = 0;
        foreach ($seq as $uuid) {
            self::assertEquals($uuids[$i], $uuid->toRfc4122());
            $i += 1;
            if ($i >= \count($uuids)) {
                break;
            }
        }
    }
}
