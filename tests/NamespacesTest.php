<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\Namespaces\UuidNamespace;
use Arokettu\Uuid\UuidParser;
use Arokettu\Uuid\UuidV1;
use PHPUnit\Framework\TestCase;

class NamespacesTest extends TestCase
{
    public function testNamespaces(): void
    {
        foreach (UuidNamespace::cases() as $namespace) {
            self::assertInstanceOf(UuidV1::class, $namespace->getUuid());
        }
    }

    public function testUuid(): void
    {
        $uuid = UuidParser::fromRfcFormat('6ba7b811-9dad-11d1-80b4-00c04fd430c8'); // URL namespace

        self::assertEquals($uuid, UuidNamespace::URL->getUuid());
    }

    public function testBin(): void
    {
        $bin = "\x6b\xa7\xb8\x11\x9d\xad\x11\xd1\x80\xb4\x00\xc0\x4f\xd4\x30\xc8"; // URL namespace

        self::assertEquals($bin, UuidNamespace::URL->getBytes());
    }
}
