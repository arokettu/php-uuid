<?php

/**
 * @copyright 2023 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\Rfc4122Uuid;
use Arokettu\Uuid\Rfc9562Uuid;
use Arokettu\Uuid\TimeBasedUuid;
use Arokettu\Uuid\UlidParser;
use Arokettu\Uuid\Uuid;
use Arokettu\Uuid\UuidFactory;
use Arokettu\Uuid\UuidParser;
use Arokettu\Uuid\Variant10xxUuid;
use PHPUnit\Framework\TestCase;

final class InheritanceTest extends TestCase
{
    public function testInheritance(): void
    {
        $uuid1 = UuidParser::fromRfc4122('d3b52d12-1d93-11ee-be56-0242ac120002');
        $uuid2 = UuidParser::fromRfc4122('d9fd4772-1d93-21ee-be56-0242ac120002');
        $uuid3 = UuidParser::fromRfc4122('3e9368b6-7cd0-3eda-a261-6c9b40a53d1d');
        $uuid4 = UuidParser::fromRfc4122('eef3c36e-9d50-42bb-ab11-14e2818db34d');
        $uuid5 = UuidParser::fromRfc4122('75d684a0-03ba-504f-8963-7bfc7b520bda');
        $uuid6 = UuidParser::fromRfc4122('1ee1d940-9ca1-66c4-be56-0242ac120002');
        $uuid7 = UuidParser::fromRfc4122('018935b6-8d4e-79f2-8de9-348197e34c14');
        $uuid8 = UuidParser::fromRfc4122('f8eeb7a5-6e09-8d71-9482-a870fed8f2af');

        $ulid = UlidParser::fromBase32('01H4TVF5PDFXBSW5ZAGR9YKPMT');

        $nil = UuidFactory::nil();
        $max = UuidFactory::max();

        $generic = UuidParser::fromRfc4122('d392f69b-9177-f92e-24c9-e87d276af78b');

        // V1
        self::assertInstanceOf(Uuid::class, $uuid1);
        self::assertInstanceOf(Rfc4122Uuid::class, $uuid1);
        self::assertInstanceOf(Rfc9562Uuid::class, $uuid1);
        self::assertInstanceOf(Variant10xxUuid::class, $uuid1);
        self::assertInstanceOf(TimeBasedUuid::class, $uuid1);

        // V2
        self::assertInstanceOf(Uuid::class, $uuid2);
        self::assertNotInstanceOf(Rfc4122Uuid::class, $uuid2);
        self::assertNotInstanceOf(Rfc9562Uuid::class, $uuid2);
        self::assertInstanceOf(Variant10xxUuid::class, $uuid2);
        self::assertInstanceOf(TimeBasedUuid::class, $uuid2);

        // V3
        self::assertInstanceOf(Uuid::class, $uuid3);
        self::assertInstanceOf(Rfc4122Uuid::class, $uuid3);
        self::assertInstanceOf(Rfc9562Uuid::class, $uuid3);
        self::assertInstanceOf(Variant10xxUuid::class, $uuid3);
        self::assertNotInstanceOf(TimeBasedUuid::class, $uuid3);

        // V4
        self::assertInstanceOf(Uuid::class, $uuid4);
        self::assertInstanceOf(Rfc4122Uuid::class, $uuid4);
        self::assertInstanceOf(Rfc9562Uuid::class, $uuid4);
        self::assertInstanceOf(Variant10xxUuid::class, $uuid4);
        self::assertNotInstanceOf(TimeBasedUuid::class, $uuid4);

        // V5
        self::assertInstanceOf(Uuid::class, $uuid5);
        self::assertInstanceOf(Rfc4122Uuid::class, $uuid5);
        self::assertInstanceOf(Rfc9562Uuid::class, $uuid5);
        self::assertInstanceOf(Variant10xxUuid::class, $uuid5);
        self::assertNotInstanceOf(TimeBasedUuid::class, $uuid5);

        // V6
        self::assertInstanceOf(Uuid::class, $uuid6);
        self::assertNotInstanceOf(Rfc4122Uuid::class, $uuid6);
        self::assertInstanceOf(Rfc9562Uuid::class, $uuid6);
        self::assertInstanceOf(Variant10xxUuid::class, $uuid6);
        self::assertInstanceOf(TimeBasedUuid::class, $uuid6);

        // V7
        self::assertInstanceOf(Uuid::class, $uuid7);
        self::assertNotInstanceOf(Rfc4122Uuid::class, $uuid7);
        self::assertInstanceOf(Rfc9562Uuid::class, $uuid7);
        self::assertInstanceOf(Variant10xxUuid::class, $uuid7);
        self::assertInstanceOf(TimeBasedUuid::class, $uuid7);

        // V8
        self::assertInstanceOf(Uuid::class, $uuid8);
        self::assertNotInstanceOf(Rfc4122Uuid::class, $uuid8);
        self::assertInstanceOf(Rfc9562Uuid::class, $uuid8);
        self::assertInstanceOf(Variant10xxUuid::class, $uuid8);
        self::assertNotInstanceOf(TimeBasedUuid::class, $uuid8);

        // Ulid
        self::assertInstanceOf(Uuid::class, $ulid);
        self::assertNotInstanceOf(Rfc4122Uuid::class, $ulid);
        self::assertNotInstanceOf(Rfc9562Uuid::class, $ulid);
        self::assertNotInstanceOf(Variant10xxUuid::class, $ulid);
        self::assertInstanceOf(TimeBasedUuid::class, $ulid);

        // Nil
        self::assertInstanceOf(Uuid::class, $nil);
        self::assertInstanceOf(Rfc4122Uuid::class, $nil);
        self::assertInstanceOf(Rfc9562Uuid::class, $nil);
        self::assertNotInstanceOf(Variant10xxUuid::class, $nil);
        self::assertNotInstanceOf(TimeBasedUuid::class, $nil);

        // Max
        self::assertInstanceOf(Uuid::class, $max);
        self::assertNotInstanceOf(Rfc4122Uuid::class, $max);
        self::assertInstanceOf(Rfc9562Uuid::class, $max);
        self::assertNotInstanceOf(Variant10xxUuid::class, $max);
        self::assertNotInstanceOf(TimeBasedUuid::class, $max);

        // Generic
        self::assertInstanceOf(Uuid::class, $generic);
        self::assertNotInstanceOf(Rfc4122Uuid::class, $generic);
        self::assertNotInstanceOf(Rfc9562Uuid::class, $generic);
        self::assertNotInstanceOf(Variant10xxUuid::class, $generic);
        self::assertNotInstanceOf(TimeBasedUuid::class, $generic);
    }
}
