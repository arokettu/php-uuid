<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\Ulid;
use Arokettu\Uuid\UuidFactory;
use Arokettu\Uuid\UuidParser;
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
}
