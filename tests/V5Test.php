<?php

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\UuidFactory;
use PHPUnit\Framework\TestCase;

class V5Test extends TestCase
{
    public function testNil(): void
    {
        $uuid = UuidFactory::v5(UuidFactory::nil(), 'test');

        self::assertEquals('e8b764da-5fe5-51ed-8af8-c5c6eca28d7a', $uuid->toRfc4122());
    }
}
