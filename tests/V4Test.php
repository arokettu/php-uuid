<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\Tests\Helper\FixedSequenceEngine;
use Arokettu\Uuid\UuidFactory;
use PHPUnit\Framework\TestCase;
use Random\Engine;
use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

class V4Test extends TestCase
{
    public function testMin(): void
    {
        $uuid = UuidFactory::v4(new Randomizer(new FixedSequenceEngine("\0")));

        self::assertEquals('00000000-0000-4000-8000-000000000000', $uuid->toRfc4122());
    }

    public function testMax(): void
    {
        $uuid = UuidFactory::v4(new Randomizer(new FixedSequenceEngine("\xff")));

        self::assertEquals('ffffffff-ffff-4fff-bfff-ffffffffffff', $uuid->toRfc4122());
    }

    public function testRandom(): void
    {
        $uuid = UuidFactory::v4(new Randomizer(new Xoshiro256StarStar(123))); // f969a0d1a18f5a325e4d6d65c7e335f8

        self::assertEquals('f969a0d1-a18f-4a32-9e4d-6d65c7e335f8', $uuid->toRfc4122());
    }
}
