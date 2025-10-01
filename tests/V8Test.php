<?php

/**
 * @copyright 2023 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Uuid\Tests;

use Arokettu\Uuid\UuidFactory;
use Arokettu\Uuid\UuidV8;
use PHPUnit\Framework\TestCase;
use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

final class V8Test extends TestCase
{
    public function testMin(): void
    {
        $uuid = UuidFactory::v8(str_repeat("\0", 16));

        self::assertEquals('00000000-0000-8000-8000-000000000000', $uuid->toRfc4122());
    }

    public function testMax(): void
    {
        $uuid = UuidFactory::v8(str_repeat("\xff", 16));

        self::assertEquals('ffffffff-ffff-8fff-bfff-ffffffffffff', $uuid->toRfc4122());
    }

    public function testRandom(): void
    {
        $bytes = (new Randomizer(new Xoshiro256StarStar(123)))->getBytes(16); // f969a0d1a18f5a325e4d6d65c7e335f8
        $uuid = UuidFactory::v8($bytes);

        self::assertEquals('f969a0d1-a18f-8a32-9e4d-6d65c7e335f8', $uuid->toString());
    }

    public function testWrongLength(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('$bytes must be 16 bytes long');

        $bytes = (new Randomizer(new Xoshiro256StarStar(123)))->getBytes(15); // f969a0d1a18f5a325e4d6d65c7e335

        UuidFactory::v8($bytes);
    }

    public function testDirectCreation(): void
    {
        $bytes = (new Randomizer(new Xoshiro256StarStar(123)))->getBytes(16); // f969a0d1a18f5a325e4d6d65c7e335f8
        $bytes[6] = "\x8a"; // valid version upper hex (8)
        $bytes[8] = "\x9e"; // valid variant bits in upper hex (89ab)

        $uuid = new UuidV8(bin2hex($bytes));
        self::assertEquals('f969a0d1-a18f-8a32-9e4d-6d65c7e335f8', $uuid->toString());
    }

    public function testDirectCreationWrongLength(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('$hex must be 32 lowercase hexadecimal digits');

        $bytes = (new Randomizer(new Xoshiro256StarStar(123)))->getBytes(15); // f969a0d1a18f5a325e4d6d65c7e335
        $bytes[6] = "\x8a"; // valid version upper hex (8)
        $bytes[8] = "\x9e"; // valid variant bits in upper hex (89ab)

        new UuidV8(bin2hex($bytes));
    }

    public function testDirectCreationWrongVariant(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('The supplied UUID is not a valid RFC 9562 UUID');

        $bytes = (new Randomizer(new Xoshiro256StarStar(123)))->getBytes(16); // f969a0d1a18f5a325e4d6d65c7e335f8
        $bytes[6] = "\x8a"; // valid version upper hex (8)

        new UuidV8(bin2hex($bytes));
    }

    public function testDirectCreationWrongVersion(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('The supplied UUID is not a valid RFC 9562 version 8 UUID');

        $bytes = (new Randomizer(new Xoshiro256StarStar(123)))->getBytes(16); // f969a0d1a18f5a325e4d6d65c7e335f8
        $bytes[8] = "\x9e"; // valid variant bits in upper hex (89ab)

        new UuidV8(bin2hex($bytes));
    }
}
