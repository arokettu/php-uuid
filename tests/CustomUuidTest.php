<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\NonStandard\CustomUuidFactory;
use Arokettu\Uuid\UuidNamespaces;
use PHPUnit\Framework\TestCase;

class CustomUuidTest extends TestCase
{
    public function testSha256(): void
    {
        // rfc example
        $uuid = CustomUuidFactory::sha256(UuidNamespaces::dns(), 'www.example.com');

        self::assertEquals('5c146b14-3c52-8afd-938a-375d0df1fbf6', $uuid->toString());
    }
}
