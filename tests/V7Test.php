<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Clock\StaticClock;
use Arokettu\Uuid\Tests\Helper\FixedSequenceEngine;
use Arokettu\Uuid\UuidFactory;
use PHPUnit\Framework\TestCase;
use Random\Randomizer;

class V7Test extends TestCase
{
    public function testMin(): void
    {
        $uuid = UuidFactory::v7(
            4,
            new Randomizer(new FixedSequenceEngine("\0")),
            new StaticClock(new \DateTime('@0')),
        );

        self::assertEquals('00000000-0000-7000-8000-000000000000', $uuid->toRfc4122());
    }
}
