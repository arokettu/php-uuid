<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\Namespaces\UuidNamespace;
use Arokettu\Uuid\UuidFactory;
use PHPUnit\Framework\TestCase;

final class V3Test extends TestCase
{
    public function testNil(): void
    {
        $uuid = UuidFactory::v3(UuidFactory::nil(), 'test');

        self::assertEquals('96e17d7a-ac89-38cf-95e1-bf5098da34e1', $uuid->toRfc4122());
    }

    public function testRfcExample(): void
    {
        $uuid = UuidFactory::v3(UuidNamespace::DNS, 'www.example.com');

        self::assertEquals('5df41881-3aed-3515-88a7-2f4a814cf09e', $uuid->toString());
    }
}
