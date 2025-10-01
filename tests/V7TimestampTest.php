<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Clock\StaticClock;
use Arokettu\Uuid\UuidFactory;
use Arokettu\Uuid\UuidParser;
use Arokettu\Uuid\UuidV7;
use PHPUnit\Framework\TestCase;

final class V7TimestampTest extends TestCase
{
    public function testTimestamp(): void
    {
        /** @var UuidV7 $uuid */
        $uuid = UuidParser::fromString('00000000-0000-7012-a345-6789abcdef00');
        self::assertEquals(new \DateTime('@0'), $uuid->getDateTime()); // epoch

        /** @var UuidV7 $uuid */
        $uuid = UuidParser::fromString('01000000-0000-7012-a345-6789abcdef00');
        self::assertEquals(new \DateTime('@1099511627.776'), $uuid->getDateTime()); // 2004 something

        /** @var UuidV7 $uuid */
        $uuid = UuidParser::fromString('feffffff-ffff-7012-a345-6789abcdef00');
        self::assertEquals(new \DateTime('@280375465082.879'), $uuid->getDateTime());

        /** @var UuidV7 $uuid */
        $uuid = UuidParser::fromString('7fffffff-ffff-7012-a345-6789abcdef00');
        self::assertEquals(new \DateTime('@140737488355.327'), $uuid->getDateTime()); // max signed positive

        /** @var UuidV7 $uuid */
        $uuid = UuidParser::fromString('80000000-0000-7012-a345-6789abcdef00');
        self::assertEquals(new \DateTime('@140737488355.328'), $uuid->getDateTime()); // still positive
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
