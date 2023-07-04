<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\UuidNamespaces;
use Arokettu\Uuid\UuidV6;
use PHPUnit\Framework\TestCase;

class V6Test extends TestCase
{
    public function testTimestamp(): void
    {
        // {1d19dad6-ba7b-6811-80b4-00c04fd430c8}
        $uuid = new UuidV6(hex2bin('1d19dad6ba7b681180b400c04fd430c8'));
        $ts = new \DateTime('1998-02-04T22:13:53.151182+0000');
        self::assertEquals($ts, $uuid->getDateTime());
    }

    public function testToUuidV6(): void
    {
        $uuid = new UuidV6(hex2bin('1d19dad6ba7b681180b400c04fd430c8'));
        self::assertEquals(UuidNamespaces::url(), $uuid->toUuidV1());
    }
}
