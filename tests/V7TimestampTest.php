<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Clock\StaticClock;
use Arokettu\Uuid\UuidFactory;
use Arokettu\Uuid\UuidParser;
use Arokettu\Uuid\UuidV7;
use PHPUnit\Framework\TestCase;

class V7TimestampTest extends TestCase
{
    public function testTimestamp(): void
    {
        /** @var UuidV7 $uuid */
        $uuid = UuidParser::fromString('00000000-0000-7012-a345-6789abcdef00');
        self::assertEquals(0, $uuid->getDateTime()->getTimestamp()); // epoch

        /** @var UuidV7 $uuid */
        $uuid = UuidParser::fromString('01000000-0000-7012-a345-6789abcdef00');
        self::assertEquals(1099511627, $uuid->getDateTime()->getTimestamp()); // 2004 something

        /** @var UuidV7 $uuid */
        $uuid = UuidParser::fromString('feffffff-ffff-7012-a345-6789abcdef00');
        self::assertEquals(280375465082, $uuid->getDateTime()->getTimestamp());

        /** @var UuidV7 $uuid */
        $uuid = UuidParser::fromString('7fffffff-ffff-7012-a345-6789abcdef00');
        self::assertEquals(140737488355, $uuid->getDateTime()->getTimestamp()); // max signed positive

        /** @var UuidV7 $uuid */
        $uuid = UuidParser::fromString('80000000-0000-7012-a345-6789abcdef00');
        self::assertEquals(140737488355, $uuid->getDateTime()->getTimestamp()); // still positive
    }

    public function testMillisec(): void
    {
        // positive timestamp
        $dt = new \DateTime('2000-01-02 12:34:56.789 +0000');
        $uuid = UuidFactory::v7(new StaticClock($dt));
        self::assertEquals($dt, $uuid->getDateTime());

        // negative timestamp
        $dt = new \DateTime('1920-01-02 12:34:56.789 +0000'); // -1577791503211
        $uuid = UuidFactory::v7(new StaticClock($dt));
        // interpreted as unsigned (279897185207445)
        self::assertEquals(new \DateTimeImmutable('@279897185207.445'), $uuid->getDateTime());
    }
}
