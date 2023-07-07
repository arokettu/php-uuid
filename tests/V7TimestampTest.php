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
        self::assertEquals(-1099511628, $uuid->getDateTime()->getTimestamp()); // 1935 something

        /** @var UuidV7 $uuid */
        $uuid = UuidParser::fromString('7fffffff-ffff-7012-a345-6789abcdef00');
        self::assertEquals(140737488355, $uuid->getDateTime()->getTimestamp()); // max positive, year 6429

        /** @var UuidV7 $uuid */
        $uuid = UuidParser::fromString('80000000-0000-7012-a345-6789abcdef00');
        self::assertEquals(-140737488356, $uuid->getDateTime()->getTimestamp()); // min negative, year 2491 BCE
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
        self::assertEquals(new \DateTimeImmutable('10839-08-03T18:06:47.445000+0000'), $uuid->getDateTime());
    }
}
