<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\UuidV2;
use PHPUnit\Framework\TestCase;

class V2Test extends TestCase
{
    public function testTimestamp(): void
    {
        // rare example found in the internet wilds
        // {000004d2-92e8-21ed-8100-3fdb0085247e}
        $uuid = new UuidV2('000004d292e821ed81003fdb0085247e');
        $ts = new \DateTime('2023-01-13T02:14:24.842137+0000');

        self::assertEquals($ts, $uuid->getDateTime());
    }
}
