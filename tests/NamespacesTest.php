<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\UuidNamespaces;
use Arokettu\Uuid\UuidParser;
use PHPUnit\Framework\TestCase;

class NamespacesTest extends TestCase
{
    public function testNamespaces(): void
    {
        self::assertEquals(UuidParser::fromString(UuidNamespaces::DNS), UuidNamespaces::dns());
        self::assertEquals(UuidParser::fromString(UuidNamespaces::URL), UuidNamespaces::url());
        self::assertEquals(UuidParser::fromString(UuidNamespaces::OID), UuidNamespaces::oid());
        self::assertEquals(UuidParser::fromString(UuidNamespaces::X500), UuidNamespaces::x500());
    }
}
