<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use Arokettu\Clock\SystemClock;
use Psr\Clock\ClockInterface;
use Random\Randomizer;

final class UuidFactory
{
    private static ?string $v7timestamp = null;
    private static int $v7counter = 0;

    public static function nil(): CustomUuid
    {
        return new CustomUuid("\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
    }

    public static function max(): CustomUuid
    {
        return new CustomUuid("\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff");
    }

    public static function v4(Randomizer $randomizer = new Randomizer()): UuidV4
    {
        $bytes = $randomizer->getBytes(16);

        // set variant
        $bytes[8] = \chr(0b10 << 6 | \ord($bytes[8]) & 0b111111); // Variant 1: set the highest 2 bits to bin 10
        // set version
        $bytes[6] = \chr(0x4 << 4 | \ord($bytes[6]) & 0b1111); // Version 4: set the highest 4 bits to hex '4'

        return new UuidV4($bytes);
    }

    public static function v7(
        int $counterBits = 4,
        Randomizer $randomizer = new Randomizer(),
        ClockInterface $clock = new SystemClock(),
    ): UuidV7 {
        if ($counterBits < 0 || $counterBits > 12) {
            throw new \ValueError('$counterBits must be in range 0-12');
        }

        $now = $clock->now();
        $ts = $now->format('Uv');

        if (PHP_INT_SIZE >= 8) {
            // 64 bit

            // 48 bit (6 byte) timestamp
            $bytesTS = hex2bin(str_pad(dechex(\intval($ts)), 12, '0', STR_PAD_LEFT));
            if (\strlen($bytesTS) > 6) {
                $bytesTS = substr($bytesTS, -6); // allow date to roll over on 10889-08-02 lol
            }
        } else {
            throw new \LogicException('32 bit not implemented'); // todo
        }

        if ($bytesTS === self::$v7timestamp) {
            self::$v7counter += 1;
            if (self::$v7counter >= 2 ** $counterBits) {
                // do not allow counter rollover
                throw new \RuntimeException(sprintf(
                    "For %d counter bits batch must be shorter than %d",
                    $counterBits,
                    2 ** $counterBits,
                ));
            }
        } else {
            self::$v7timestamp = $bytesTS;
        }

        $bytes = $bytesTS . $randomizer->getBytes(10);

        // set variant
        $bytes[8] = \chr(0b10 << 6 | \ord($bytes[8]) & 0b111111); // Variant 1: set the highest 2 bits to bin 10

        // set version and counter
        // Version 7: set the highest 4 bits to hex '7'
        if ($counterBits === 4) {
            $bytes[6] = \chr(0x7 << 4 | self::$v7counter);
        } elseif ($counterBits < 4) {
            $randomBits = 4 - $counterBits;
            $version = 0x7 << 4;
            $counter = self::$v7counter << $randomBits;
            $random = \ord($bytes[6]) % 2 ** $randomBits;
            $bytes[6] = \chr($version | $counter | $random);
        } else {
            $byte7CounterBits = $counterBits - 4;
            $randomBits = 8 - $byte7CounterBits;
            $bytes[6] = \chr(0x7 << 4 | self::$v7counter >> $byte7CounterBits);
            $counter = self::$v7counter << $randomBits;
            $random = \ord($bytes[7]) % 2 ** $randomBits;
            $bytes[7] = \chr($counter | $random);
        }

        return new UuidV7($bytes);
    }

    public static function v8(string $bytes): UuidV8
    {
        if (\strlen($bytes) !== 16) {
            throw new \ValueError('$bytes must be 16 bytes long');
        }

        // set variant
        $bytes[8] = \chr(0b10 << 6 | \ord($bytes[8]) & 0b111111); // Variant 1: set the highest 2 bits to bin 10
        // set version
        $bytes[6] = \chr(0x8 << 4 | \ord($bytes[6]) & 0b1111); // Version 8: set the highest 4 bits to hex '8'

        return new UuidV8($bytes);
    }
}
