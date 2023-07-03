<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\Ulid;
use Arokettu\Uuid\UuidParser;
use PHPUnit\Framework\TestCase;

class UlidV7ConversionTest extends TestCase
{
    public function testIsCompatible(): void
    {
        /** @var Ulid $ulid1 */
        /** @var Ulid $ulid2 */
        $ulid1 = UuidParser::fromString('01HF7YAT00Z5MT1MD1HXD34QJD');
        $ulid2 = UuidParser::fromString('01HF7YAT00F5MT1MD1HXD34QJD');

        self::assertFalse($ulid1->isUuidV7Compatible());
        self::assertTrue($ulid2->isUuidV7Compatible());
    }

    public function testConversion(): void
    {
        /** @var Ulid $ulid1 */
        /** @var Ulid $ulid2 */
        $ulid1 = UuidParser::fromString('01HF7YAT00Z5MT1MD1HXD34QJD');
        $ulid2 = UuidParser::fromString('01HF7YAT00F5MT1MD1HXD34QJD');

        self::assertEquals('018bcfe5-6800-7969-a0d1-a18f5a325e4d', $ulid1->toUuidV7(lossy: true)->toString());
        self::assertEquals('018bcfe5-6800-7969-a0d1-a18f5a325e4d', $ulid2->toUuidV7()->toString());
        self::assertEquals('018bcfe5-6800-7969-a0d1-a18f5a325e4d', $ulid2->toUuidV7(lossy: true)->toString());
    }

    public function testLosslessConversionNotPossible(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('This ULID cannot be converted to UUID v7 losslessly');

        /** @var Ulid $ulid1 */
        $ulid1 = UuidParser::fromString('01HF7YAT00Z5MT1MD1HXD34QJD');

        $ulid1->toUuidV7()->toString();
    }
}
