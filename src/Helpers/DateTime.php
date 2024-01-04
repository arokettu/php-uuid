<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

use Arokettu\Unsigned as u;
use DateTimeImmutable;
use DateTimeInterface;
use RuntimeException;

/**
 * @internal
 */
final class DateTime
{
    private const V1_EPOCH = -12219292800; // (new DateTimeImmutable('1582-10-15 UTC'))->getTimestamp()
    private const V1_EPOCH_STR_NEG = '2d8539c80'; // 12219292800. it's 34 bit so string for 32

    public static function buildUlidHex(DateTimeInterface $dt): string
    {
        $tsS  = $dt->format('U');
        $tsMs = $dt->format('v');

        if (PHP_INT_SIZE >= 8) {
            // 64 bit
            $ts = \intval($tsS) * 1000 + \intval($tsMs);

            // 48 bit (6 byte) timestamp
            $hexTS = dechex($ts);
            if (\strlen($hexTS) > 12) {
                $hexTS = substr($hexTS, -12); // allow date to roll over on 10889-08-02 lol
            }

            return sprintf('%012s', $hexTS);
        // @codeCoverageIgnoreStart
        // 32 bit stuff is not covered by the coverage build
        } elseif (\extension_loaded('gmp')) {
            // gmp
            $ts = gmp_init($tsS . '000') + \intval($tsMs);
            if ($ts >= 0) {
                $hexTS = gmp_strval($ts, 16);
            } else {
                $hexTS = bin2hex(~gmp_export($ts + 1, 6, GMP_BIG_ENDIAN));
            }

            // 48 bit (6 byte) timestamp
            if (\strlen($hexTS) > 12) {
                $hexTS = substr($hexTS, -12); // allow date to roll over on 10889-08-02 lol
            }

            return sprintf('%012s', $hexTS);
        } else {
            // 32 bit, no gmp

            if (str_starts_with($tsS, '-')) {
                $tsSU = u\neg(u\from_dec(substr($tsS, 1) . '000', 6));
            } else {
                $tsSU = u\from_dec($tsS . '000', 6);
            }

            return u\to_hex(u\add_int($tsSU, \intval($tsMs)));
        }
        // @codeCoverageIgnoreEnd
    }

    public static function parseUlidHex(string $hex): DateTimeImmutable
    {
        if (PHP_INT_SIZE >= 8) { // 64 bit - a simple way
            $tsMs = hexdec($hex);
            $ts = intdiv($tsMs, 1000);
            $ms = $tsMs % 1000;
            return DateTimeImmutable::createFromFormat('U u', sprintf('%d %03d', $ts, $ms)) ?:
                throw new RuntimeException('Error creating DateTime object');
        // @codeCoverageIgnoreStart
        // 32 bit stuff is not covered by the coverage build
        } elseif (\extension_loaded('gmp')) {
            $tsMs = gmp_init($hex, 16);
            [$ts, $ms] = gmp_div_qr($tsMs, 1000);
            return DateTimeImmutable::createFromFormat('U u', sprintf('%s %03s', gmp_strval($ts), gmp_strval($ms))) ?:
                throw new RuntimeException('Error creating DateTime object');
        } else {
            $tsMs = u\from_hex($hex, 6);
            [$ts, $ms] = u\div_mod_int($tsMs, 1000);
            return DateTimeImmutable::createFromFormat('U u', sprintf('%s %03d', u\to_dec($ts), $ms)) ?:
                throw new RuntimeException('Error creating DateTime object');
        }
        // @codeCoverageIgnoreEnd
    }

    public static function buildUuidV1Hex(DateTimeInterface $dt, int $nsec100 = 0): string
    {
        $tsS  = $dt->format('U');
        $tsUs = $dt->format('u');

        if (PHP_INT_SIZE >= 8) {
            $ts = (\intval($tsS) - self::V1_EPOCH) * 10_000_000 + \intval($tsUs) * 10 + $nsec100;

            // 60 bit (7.5 byte / 15 hex digit) timestamp
            $hexTS = dechex($ts);
            if (\strlen($hexTS) > 15) {
                $hexTS = substr($hexTS, -15); // allow date to roll over on 5236-03-31 lol
            }

            return sprintf('%015s', $hexTS);
        // @codeCoverageIgnoreStart
        // 32 bit stuff is not covered by the coverage build
        } elseif (\extension_loaded('gmp')) {
            $ts = (gmp_init($tsS, 10) + gmp_init(self::V1_EPOCH_STR_NEG, 16)) * 10_000_000 +
                \intval($tsUs) * 10 + $nsec100;

            // 60 bit (7.5 byte / 15 hex digit) timestamp
            if ($ts >= 0) {
                $hexTS = gmp_strval($ts, 16);
            } else {
                $hexTS = bin2hex(~gmp_export($ts + 1, 8, GMP_BIG_ENDIAN));
            }
            if (\strlen($hexTS) > 15) {
                $hexTS = substr($hexTS, -15); // allow date to roll over on 5236-03-31 lol
            }

            return sprintf('%015s', $hexTS);
        } else {
            if ($tsS[0] === '-') {
                $tsS = substr($tsS, 1);
                $ts = u\neg(u\from_dec($tsS, 8));
            } else {
                $ts = u\from_dec($tsS, 8);
            }
            $ts = u\add($ts, u\from_hex(self::V1_EPOCH_STR_NEG, 8));
            $ts = u\mul_int($ts, 10_000_000);
            $ts = u\add_int($ts, \intval($tsUs) * 10 + $nsec100);

            return substr(u\to_hex($ts), -15);
        }
        // @codeCoverageIgnoreEnd
    }

    public static function parseUuidV1Hex(string $hex): DateTimeImmutable
    {
        // 100-nanosecond intervals since midnight 15 October 1582 UTC

        if (PHP_INT_SIZE >= 8) {
            // 64 bit - a simple way
            $ts = hexdec($hex);
            $tsS = intdiv($ts, 10_000_000) + self::V1_EPOCH; // convert to unix timestamp
            $tsUs = intdiv($ts % 10_000_000, 10); // lose 1 decimal of precision

            return DateTimeImmutable::createFromFormat('U u', sprintf('%d %06d', $tsS, $tsUs)) ?:
                throw new RuntimeException('Error creating DateTime object');
        // @codeCoverageIgnoreStart
        // 32 bit stuff is not covered by the coverage build
        } elseif (\extension_loaded('gmp')) {
            $ts = gmp_init($hex, 16);
            [$tsES, $tsNs] = gmp_div_qr($ts, 10_000_000); // epoch and hundreds of nanoseconds
            $tsS = $tsES - gmp_init(self::V1_EPOCH_STR_NEG, 16);
            $tsUs = gmp_div($tsNs, 10);

            return DateTimeImmutable::createFromFormat(
                'U u',
                sprintf('%s %06s', gmp_strval($tsS), gmp_strval($tsUs))
            ) ?: throw new RuntimeException('Error creating DateTime object');
        } else {
            $ts = u\from_hex($hex, 8);
            [$tsES, $tsNs] = u\div_mod_int($ts, 10_000_000); // epoch and hundreds of nanoseconds
            $tsS = u\sub($tsES, u\from_hex(self::V1_EPOCH_STR_NEG, 8));
            $tsUs = intdiv($tsNs, 10); // lose 1 decimal of precision

            return DateTimeImmutable::createFromFormat('U u', sprintf('%s %06d', u\to_dec($tsS), $tsUs)) ?:
                throw new RuntimeException('Error creating DateTime object');
        }
        // @codeCoverageIgnoreEnd
    }
}
