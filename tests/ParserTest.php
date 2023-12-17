<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\GenericUuid;
use Arokettu\Uuid\MaxUuid;
use Arokettu\Uuid\NilUuid;
use Arokettu\Uuid\UlidParser;
use Arokettu\Uuid\UuidParser;
use Arokettu\Uuid\UuidV1;
use Arokettu\Uuid\UuidV2;
use Arokettu\Uuid\UuidV3;
use Arokettu\Uuid\UuidV4;
use Arokettu\Uuid\UuidV5;
use Arokettu\Uuid\UuidV6;
use Arokettu\Uuid\UuidV7;
use Arokettu\Uuid\UuidV8;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function testTypeDetection(): void
    {
        // Known UUID types
        self::assertInstanceOf(UuidV1::class, UuidParser::fromString('C12A7328-F81F-11D2-BA4B-00A0C93EC93B'));
        self::assertInstanceOf(UuidV2::class, UuidParser::fromString('000003e8-113f-21ee-8c00-2eb5a363657c'));
        self::assertInstanceOf(UuidV3::class, UuidParser::fromString('a3bb189e-8bf9-3888-9912-ace4e6543002'));
        self::assertInstanceOf(UuidV4::class, UuidParser::fromString('BFBFAFE7-A34F-448A-9A5B-6213EB736C22'));
        self::assertInstanceOf(UuidV5::class, UuidParser::fromString('3DE21764-95BD-54BD-A5C3-4ABE786F38A8'));
        self::assertInstanceOf(UuidV6::class, UuidParser::fromString('1d2f81fc-12a7-6328-ba4b-00a0c93ec93b'));
        self::assertInstanceOf(UuidV7::class, UuidParser::fromString('0188e4eb-268f-7be0-9fe5-c8c4602c92c3'));
        self::assertInstanceOf(UuidV8::class, UuidParser::fromString('b3ea190d-e910-876d-a28f-f0a4a2af30bb'));

        // Special UUIDs
        self::assertInstanceOf(NilUuid::class, UuidParser::fromString('00000000-0000-0000-0000-000000000000'));
        self::assertInstanceOf(MaxUuid::class, UuidParser::fromString('FFFFFFFF-FFFF-FFFF-FFFF-FFFFFFFFFFFF'));

        // Not a known version
        self::assertInstanceOf(GenericUuid::class, UuidParser::fromString('115df02f-042e-91b7-bba9-c575cffde2dc'));

        // Not Variant 1
        self::assertInstanceOf(GenericUuid::class, UuidParser::fromString('e3be9143-c203-45b2-0af6-332b8ce0b069'));

        // Base32 should now parse to uuid too
        self::assertInstanceOf(GenericUuid::class, UuidParser::fromString('01H3JF5GX4M2D891JB7AYDDH6H'));
    }

    public function testBytes(): void
    {
        $uuid = UuidParser::fromBytes('1234567890123456');

        self::assertEquals('31323334-3536-3738-3930-313233343536', $uuid->toString());
    }

    public function testBytesWrongLength(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('UUID must be 16 bytes long');

        UuidParser::fromBytes('12345678901234567');
    }

    public function testHexWrongLength(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('UUID must be 32 hexadecimal digits');

        UuidParser::fromHex('3132333435363738393031323334353637');
    }

    public function testHexWrongLengthUlid(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('ULID must be 32 hexadecimal digits');

        UlidParser::fromHex('3132333435363738393031323334353637');
    }

    public function testRfcWrongChars(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Not a valid RFC 4122 UUID notation');

        UuidParser::fromRfc4122('000003e8-113f-21ee-8z00-2eb5a363657c');
    }

    public function testBase32WrongChars1(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Not a valid Base32 encoded ULID');

        UlidParser::fromBase32('ZZZZZZZZZZZZZZZZZZZZZZZZZZ');
    }

    public function testBase32WrongCharsU(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Not a valid Base32 encoded ULID');

        UlidParser::fromBase32('7ZZZZZZZZZZZUZZZZZZZZZZZZZ');
    }

    public function testBase32FirstChar(): void
    {
        // all valid first digits are correctly parsed
        $values = [
            ['10842108-4210-8421-0842-108421084210', '0ggggggggggggggggggggggggg'],
            ['30842108-4210-8421-0842-108421084210', '1ggggggggggggggggggggggggg'],
            ['50842108-4210-8421-0842-108421084210', '2ggggggggggggggggggggggggg'],
            ['70842108-4210-8421-0842-108421084210', '3ggggggggggggggggggggggggg'],
            ['90842108-4210-8421-0842-108421084210', '4ggggggggggggggggggggggggg'],
            ['b0842108-4210-8421-0842-108421084210', '5ggggggggggggggggggggggggg'],
            ['d0842108-4210-8421-0842-108421084210', '6ggggggggggggggggggggggggg'],
            ['f0842108-4210-8421-0842-108421084210', '7ggggggggggggggggggggggggg'],
            // alt
            ['10842108-4210-8421-0842-108421084210', 'oggggggggggggggggggggggggg'],
            ['30842108-4210-8421-0842-108421084210', 'iggggggggggggggggggggggggg'],
            ['30842108-4210-8421-0842-108421084210', 'lggggggggggggggggggggggggg'],
        ];

        foreach ($values as [$rfc, $base32]) {
            self::assertEquals($rfc, UlidParser::fromBase32($base32)->toRfc4122());
        }
    }

    public function testBase32AltChars(): void
    {
        $ulid1 = UlidParser::fromBase32('01101101101101101101101101');
        $ulid2 = UlidParser::fromBase32('OILOILOILOILOILOILOILOILOI');

        self::assertEquals($ulid1, $ulid2);
    }
}
