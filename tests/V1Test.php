<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\UuidNamespaces;
use Arokettu\Uuid\UuidV1;
use PHPUnit\Framework\TestCase;

class V1Test extends TestCase
{
    public function testTimestamp(): void
    {
        $uuid = UuidNamespaces::url();
        $ts = new \DateTime('1998-02-04T22:13:53.151182+0000');
        self::assertEquals($ts, $uuid->getDateTime());

        // check with the same timestamp as UUIDv2 example
        // {00000002-92e8-11ed-8100-3fdb0085247e}
        $uuid = new UuidV1(hex2bin('0000000292e811ed81003fdb0085247e'));
        $ts = new \DateTime('2023-01-13T02:14:24.842137+0000');
        self::assertEquals($ts, $uuid->getDateTime());
    }

    public function testToUuidV6(): void
    {
        $uuid = UuidNamespaces::url();
        self::assertEquals('1d19dad6-ba7b-6811-80b4-00c04fd430c8', $uuid->toUuidV6()->toString());
    }
}
