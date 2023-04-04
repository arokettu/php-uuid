<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\UuidFactory;
use PHPUnit\Framework\TestCase;
use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

class V8Test extends TestCase
{
    public function testMin(): void
    {
        $uuid = UuidFactory::v8(str_repeat("\0", 16));

        self::assertEquals('00000000-0000-8000-8000-000000000000', $uuid->toRfc4122());
    }

    public function testMax(): void
    {
        $uuid = UuidFactory::v8(str_repeat("\xff", 16));

        self::assertEquals('ffffffff-ffff-8fff-bfff-ffffffffffff', $uuid->toRfc4122());
    }

    public function testRandom(): void
    {
        $bytes = (new Randomizer(new Xoshiro256StarStar(123)))->getBytes(16); // f969a0d1a18f5a325e4d6d65c7e335f8
        $uuid = UuidFactory::v8($bytes);

        self::assertEquals('f969a0d1-a18f-8a32-9e4d-6d65c7e335f8', $uuid->toRfc4122());
    }
}
