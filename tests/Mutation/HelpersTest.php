<?php

/**
 * @copyright 2023 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Uuid\Tests\Mutation;

use Arokettu\Uuid\Helpers\UuidBytes;
use Arokettu\Uuid\Helpers\UuidVariant;
use Arokettu\Uuid\NilUuid;
use PHPUnit\Framework\TestCase;

final class HelpersTest extends TestCase
{
    public function testNoSettingInvalidVersionTooLow(): void
    {
        $hex = NilUuid::HEX;

        $this->expectException(\LogicException::class);

        UuidBytes::setVersion($hex, 0);
    }

    public function testNoSettingInvalidVersionTooHigh(): void
    {
        $hex = NilUuid::HEX;

        $this->expectException(\LogicException::class);

        UuidBytes::setVersion($hex, 9);
    }

    public function testOnlyV10xx(): void
    {
        $hex = NilUuid::HEX;

        $this->expectException(\LogicException::class);

        UuidBytes::setVariant($hex, UuidVariant::v0xxx);
    }

    public function testV1(): void
    {
        // no v1 factory currently uses the UuidBytes::setVersion helper, test it explicitly

        $hex = NilUuid::HEX;

        UuidBytes::setVersion($hex, 1);

        self::assertEquals('00000000000010000000000000000000', $hex);
    }
}
