<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Clock\StaticClock;
use Arokettu\Uuid\UuidFactory;
use PHPUnit\Framework\TestCase;

class UlidTimestampTest extends TestCase
{
    public function testMillisec(): void
    {
        // positive timestamp
        $dt = new \DateTime('2000-01-02 12:34:56.789 +0000');
        $uuid = UuidFactory::ulid(clock: new StaticClock($dt));
        self::assertEquals($dt, $uuid->getDateTime());

        // negative timestamp
        $dt = new \DateTime('1920-01-02 12:34:56.789 +0000'); // -1577791503211
        $uuid = UuidFactory::ulid(clock: new StaticClock($dt));
        // interpreted as unsigned (279897185207445)
        self::assertEquals(new \DateTimeImmutable('10839-08-03T18:06:47.445000+0000'), $uuid->getDateTime());
    }
}
