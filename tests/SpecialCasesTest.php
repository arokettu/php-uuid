<?php

/**
 * @copyright 2023 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\UuidFactory;
use PHPUnit\Framework\TestCase;

final class SpecialCasesTest extends TestCase
{
    public function testNil(): void
    {
        $uuid = UuidFactory::nil();

        self::assertEquals('00000000-0000-0000-0000-000000000000', $uuid->toRfc4122());
        self::assertEquals('00000000000000000000000000', $uuid->toBase32());
    }

    public function testMax(): void
    {
        $uuid = UuidFactory::max();

        self::assertEquals('ffffffff-ffff-ffff-ffff-ffffffffffff', $uuid->toRfc4122());
        self::assertEquals('7ZZZZZZZZZZZZZZZZZZZZZZZZZ', $uuid->toBase32());
    }

    public function testAllVariantBits(): void
    {
        $cases = [
            ["\x00", '00000000-0000-8000-8000-000000000000'], // 0000 -> 1000 -> 8
            ["\x11", '11111111-1111-8111-9111-111111111111'], // 0001 -> 1001 -> 9
            ["\x22", '22222222-2222-8222-a222-222222222222'], // 0010 -> 1010 -> a
            ["\x33", '33333333-3333-8333-b333-333333333333'], // 0011 -> 1011 -> b
            ["\x44", '44444444-4444-8444-8444-444444444444'], // 0100 -> 1000 -> 8
            ["\x55", '55555555-5555-8555-9555-555555555555'], // 0101 -> 1001 -> 9
            ["\x66", '66666666-6666-8666-a666-666666666666'], // 0110 -> 1010 -> a
            ["\x77", '77777777-7777-8777-b777-777777777777'], // 0111 -> 1011 -> b
            ["\x88", '88888888-8888-8888-8888-888888888888'], // 1000 -> 1000 -> 8
            ["\x99", '99999999-9999-8999-9999-999999999999'], // 1001 -> 1001 -> 9
            ["\xaa", 'aaaaaaaa-aaaa-8aaa-aaaa-aaaaaaaaaaaa'], // 1010 -> 1010 -> a
            ["\xbb", 'bbbbbbbb-bbbb-8bbb-bbbb-bbbbbbbbbbbb'], // 1011 -> 1011 -> b
            ["\xcc", 'cccccccc-cccc-8ccc-8ccc-cccccccccccc'], // 1100 -> 1000 -> 8
            ["\xdd", 'dddddddd-dddd-8ddd-9ddd-dddddddddddd'], // 1101 -> 1001 -> 9
            ["\xee", 'eeeeeeee-eeee-8eee-aeee-eeeeeeeeeeee'], // 1110 -> 1010 -> a
            ["\xff", 'ffffffff-ffff-8fff-bfff-ffffffffffff'], // 1111 -> 1011 -> b
        ];

        foreach ($cases as [$byte, $uuid]) {
            self::assertEquals($uuid, UuidFactory::v8(str_repeat($byte, 16))->toString());
        }
    }
}
