<?php

/**
 * @copyright 2023 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\Namespaces\UuidNamespace;
use Arokettu\Uuid\NonStandard\CustomUuidFactory;
use PHPUnit\Framework\TestCase;

final class CustomUuidTest extends TestCase
{
    public function testSha256(): void
    {
        // rfc example
        $uuid = CustomUuidFactory::sha256(UuidNamespace::DNS, 'www.example.com');

        self::assertEquals('5c146b14-3c52-8afd-938a-375d0df1fbf6', $uuid->toString());
    }
}
