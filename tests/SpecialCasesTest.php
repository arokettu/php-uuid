<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\UuidFactory;
use PHPUnit\Framework\TestCase;

class SpecialCasesTest extends TestCase
{
    public function testNil(): void
    {
        $uuid = UuidFactory::nil();

        self::assertEquals('00000000-0000-0000-0000-000000000000', $uuid->toRfc4122());
        self::assertEquals('00000000000000000000000000', $uuid->toBase32());
    }

    public function testMax(): void
    {
        $uuid = UuidFactory::max();

        self::assertEquals('ffffffff-ffff-ffff-ffff-ffffffffffff', $uuid->toRfc4122());
        self::assertEquals('7ZZZZZZZZZZZZZZZZZZZZZZZZZ', $uuid->toBase32());
    }
}
