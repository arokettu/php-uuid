<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\Namespaces\UuidNamespace;
use Arokettu\Uuid\UuidFactory;
use PHPUnit\Framework\TestCase;

final class V5Test extends TestCase
{
    public function testNil(): void
    {
        $uuid = UuidFactory::v5(UuidFactory::nil(), 'test');

        self::assertEquals('e8b764da-5fe5-51ed-8af8-c5c6eca28d7a', $uuid->toRfc4122());
    }

    public function testRfcExample(): void
    {
        $uuid = UuidFactory::v5(UuidNamespace::DNS, 'www.example.com');

        self::assertEquals('2ed6657d-e927-568b-95e1-2665a8aea6a2', $uuid->toString());
    }
}
