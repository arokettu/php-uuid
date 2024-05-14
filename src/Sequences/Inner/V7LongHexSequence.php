<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Sequences\Inner;

use Arokettu\Uuid\Helpers;
use DateInterval;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;
use Random\Randomizer;

/**
 * @internal
 */
final class V7LongHexSequence
{
    private const MAX_COUNTER = 0xff_ff_ff; // 24 bits to avoid signed int on 32-bit systems
    private const MAX_INCREMENT = self::MAX_COUNTER; // increment with 24 bits of randomness

    private static DateInterval $ONE_MS;

    private string $time;
    private DateTimeImmutable $dt;
    private string $hex;
    private int $counterHigh;
    private int $counterLow;

    public function __construct(
        private readonly bool $uuidV7Compatible,
        private readonly ClockInterface $clock,
        private readonly Randomizer $randomizer,
    ) {
        // init 'const' if not initialized
        self::$ONE_MS ??= DateInterval::createFromDateString('1ms');
    }

    public function next(): string
    {
        $dt = $this->clock->now();
        $time = Helpers\DateTime::buildUlidHex($dt); // we need to round to correctly compare datetime

        if (!isset($this->time) || strcmp($this->time, $time) < 0) {
            $this->dt = $dt;
            $this->time = $time;

            // a slightly nonstandard layout is used for the ULID here:
            // 48 bit timestamp + 32 bit "constant" random sequence + 24 bit high counter + 24 bit low counter

            $this->hex = $this->generateHex();
            $this->counterHigh = $this->randomizer->getInt(0, self::MAX_COUNTER);
            $this->counterLow  = $this->randomizer->getInt(0, self::MAX_COUNTER);
        } else {
            $this->counterLow += $this->randomizer->getInt(1, self::MAX_INCREMENT);

            if ($this->counterLow > self::MAX_COUNTER) {
                $this->counterLow &= self::MAX_COUNTER;
                $this->counterHigh += 1;

                if ($this->counterHigh > self::MAX_COUNTER) {
                    // do not allow counter rollover

                    $this->dt = $this->dt->add(self::$ONE_MS);
                    $this->time = Helpers\DateTime::buildUlidHex($this->dt);
                    $this->hex = $this->generateHex();
                    $this->counterHigh = $this->randomizer->getInt(0, self::MAX_COUNTER);
                    $this->counterLow  = $this->randomizer->getInt(0, self::MAX_COUNTER);
                }
            }
        }

        $hex =
            $this->time .
            $this->hex .
            sprintf('%06x', $this->counterHigh) .
            sprintf('%06x', $this->counterLow);

        return $hex;
    }

    private function generateHex(): string
    {
        $hex = bin2hex($this->randomizer->getBytes(4));

        if ($this->uuidV7Compatible) {
            // Version 7: set the highest 4 bits to hex '7'
            $hex[0] = '7';
            // Variant 1: set the highest 2 bits to bin 10
            Helpers\UuidBytes::setVariant($hex, Helpers\UuidVariant::v10xx, 4);
        }

        return $hex;
    }
}
