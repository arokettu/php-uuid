<?php

/**
 * @copyright 2023 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\UuidParser;
use PHPUnit\Framework\TestCase;

final class GuidTest extends TestCase
{
    public function testGuid(): void
    {
        $bytes = hex2bin('33221100554477668899aabbccddeeff');
        $guid  = '00112233-4455-6677-8899-aabbccddeeff';

        // successfully parsed
        $uuid = UuidParser::fromGuidBytes($bytes);
        self::assertEquals($guid, $uuid->toString());

        // successfully converted to bytes
        self::assertEquals($bytes, $uuid->toGuidBytes());
    }

    public function testGuidWrongLength(): void
    {
        $bytes = hex2bin('33221100554477668899aabbccddee');

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('GUID representation must be 16 bytes long');
        UuidParser::fromGuidBytes($bytes);
    }
}
