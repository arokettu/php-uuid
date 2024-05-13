<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\Namespaces\UuidNamespace;
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
}
