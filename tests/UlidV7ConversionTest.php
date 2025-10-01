<?php

/**
 * @copyright 2023 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\UlidParser;
use Arokettu\Uuid\UuidParser;
use PHPUnit\Framework\TestCase;

final class UlidV7ConversionTest extends TestCase
{
    public function testIsCompatible(): void
    {
        $ulid1 = UlidParser::fromString('01HF7YAT00Z5MT1MD1HXD34QJD');
        $ulid2 = UlidParser::fromString('01HF7YAT00F5MT1MD1HXD34QJD');

        self::assertFalse($ulid1->isUuidV7Compatible());
        self::assertTrue($ulid2->isUuidV7Compatible());
    }

    public function testConversion(): void
    {
        $ulid1 = UlidParser::fromString('01HF7YAT00Z5MT1MD1HXD34QJD');
        $ulid2 = UlidParser::fromString('01HF7YAT00F5MT1MD1HXD34QJD');

        self::assertEquals('018bcfe5-6800-7969-a0d1-a18f5a325e4d', $ulid1->toUuidV7(lossy: true)->toString());
        self::assertEquals('018bcfe5-6800-7969-a0d1-a18f5a325e4d', $ulid2->toUuidV7()->toString());
        self::assertEquals('018bcfe5-6800-7969-a0d1-a18f5a325e4d', $ulid2->toUuidV7(lossy: true)->toString());
    }

    public function testLosslessConversionNotPossible(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('This ULID cannot be converted to UUID v7 losslessly');

        $ulid1 = UlidParser::fromString('01HF7YAT00Z5MT1MD1HXD34QJD');

        $ulid1->toUuidV7()->toString();
    }

    public function testConversionBack(): void
    {
        $uuid = UuidParser::fromString('018bcfe5-6800-7969-a0d1-a18f5a325e4d');

        self::assertEquals('01HF7YAT00F5MT1MD1HXD34QJD', $uuid->toUlid()->toString());
    }
}
