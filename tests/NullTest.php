<?php

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\UuidFactory;
use PHPUnit\Framework\TestCase;

class NullTest extends TestCase
{
    public function testNil(): void
    {
        $uuid = UuidFactory::nil();

        self::assertEquals('00000000-0000-0000-0000-000000000000', $uuid->toRfc4122());
    }
}
