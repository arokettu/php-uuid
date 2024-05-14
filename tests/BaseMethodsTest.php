<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\Ulid;
use Arokettu\Uuid\UuidFactory;
use Arokettu\Uuid\UuidParser;
use Arokettu\Uuid\UuidV4;
use Arokettu\Uuid\UuidV7;
use PHPUnit\Framework\TestCase;

class BaseMethodsTest extends TestCase
{
    public function testSerialize(): void
    {
        $uuid = UuidFactory::v4();

        self::assertEquals($uuid, unserialize(serialize($uuid)));
    }

    public function testStringable(): void
    {
        $uuid = UuidParser::fromString('397b7e29-6c8d-4d57-9603-d81ff4aa6705');

        self::assertEquals('397b7e29-6c8d-4d57-9603-d81ff4aa6705', (string)$uuid);
    }

    public function testRfcAliases(): void
    {
        $uuid = new UuidV4('e8dc86af41134493bf2c6c74bef278ca');

        self::assertEquals('e8dc86af-4113-4493-bf2c-6c74bef278ca', $uuid->toRfcFormat());
        self::assertEquals('e8dc86af-4113-4493-bf2c-6c74bef278ca', $uuid->toRfc4122());
        self::assertEquals('e8dc86af-4113-4493-bf2c-6c74bef278ca', $uuid->toRfc9562());
    }

    public function testCompare(): void
    {
        $uuid1 = new UuidV7('01891cc3eb72728aa07338ca5c71e1bc');
        $uuid2 = new Ulid('01891cc3eb72728aa07338ca5c71e1bc');
        $uuid3 = new UuidV7('01891cc3eb72728aa07338ca5c71e1cc'); // +0x10

        self::assertTrue($uuid1->equalTo(clone $uuid1));
        self::assertFalse($uuid1->equalTo($uuid2)); // different type
        self::assertTrue($uuid1->equalTo($uuid2, strict: false)); // but same bytes
        self::assertFalse($uuid1->equalTo($uuid3, strict: false)); // different bytes

        self::assertEquals(0, $uuid1->compare(clone $uuid1)); // equal
        self::assertEquals(0, $uuid1->compare($uuid2)); // equal
        self::assertEquals(-1, $uuid1->compare($uuid3)); // $uuid1 < $uuid3
        self::assertEquals(1, $uuid3->compare($uuid1)); // $uuid3 > $uuid1
    }

    public function testDecimal(): void
    {
        $values = [
            ['f81d4fae-7dec-11d0-a765-00a0c91e6bf6', '329800735698586629295641978511506172918'],
            ['00000000-0000-0000-0000-000000000000', '0'],
            ['ffffffff-ffff-ffff-ffff-ffffffffffff', '340282366920938463463374607431768211455'],
            ['00000000-0000-0000-7fff-ffffffffffff', '9223372036854775807'], // PHP_INT_MAX
            ['6ba7b812-9dad-11d1-80b4-00c04fd430c8', '143098242562633686632406296499919794376'],
            ['12345678-9abc-def0-1234-56789abcdef0', '24197857203266734864793317670504947440'],
            ['5ce0e9a5-6015-fec5-aadf-a328ae398115', '123456789012345678901234567890123456789'],
        ];

        foreach ($values as [$rfc, $decimal]) {
            self::assertEquals($decimal, UuidParser::fromString($rfc)->toDecimal());
        }
    }
}
