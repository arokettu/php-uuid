<?php

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\UuidFactory;
use PHPUnit\Framework\TestCase;

class V3Test extends TestCase
{
    public function testNil(): void
    {
        $uuid = UuidFactory::v3(UuidFactory::nil(), 'test');

        self::assertEquals('96e17d7a-ac89-38cf-95e1-bf5098da34e1', $uuid->toRfc4122());
    }
}
