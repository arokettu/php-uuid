<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Sequences\Inner;

use Arokettu\Uuid\Helpers;
use Arokettu\Uuid\Nodes\Node;
use DateInterval;
use DateTimeImmutable;
use Psr\Clock\ClockInterface;
use Random\Randomizer;

/**
 * @internal
 */
final class V1HexSequence
{
    private const MAX_COUNTER = 9; // one decimal

    private static DateInterval $ONE_MICROSECOND;

    private DateTimeImmutable $time;
    private int $counter;

    public function __construct(
        private readonly bool $isV6,
        private readonly Node $node,
        private readonly int|null $clockSequence,
        private readonly ClockInterface $clock,
        private readonly Randomizer $randomizer,
    ) {
        // init 'const' if not initialized
        self::$ONE_MICROSECOND ??= DateInterval::createFromDateString('1usec');
    }

    public function next(): string
    {
        $time = $this->clock->now();

        if (!isset($this->time) || $this->time < $time) {
            // if time advanced, reset everything
            $this->time = $time;
            $this->counter = 0;
        } else {
            $this->counter += 1;

            if ($this->counter > self::MAX_COUNTER) {
                $this->time = $this->time->add(self::$ONE_MICROSECOND);
                $this->counter = 0;
            }
        }

        $tsHex = Helpers\DateTime::buildUuidV1Hex($this->time, $this->counter);
        $clockHex = \sprintf('%04x', ($this->clockSequence ?? $this->randomizer->getInt(0, 0x3fff)) | 0x8000); // 2 bytes
        $nodeHex = $this->node->getHex();

        if ($this->isV6) {
            $timeHiMid = substr($tsHex, 0, 12);
            $timeLow   = substr($tsHex, 12, 3);

            $hex = "{$timeHiMid}6{$timeLow}{$clockHex}{$nodeHex}";
        } else {
            $timeLow  = substr($tsHex, 7, 8);
            $timeMid  = substr($tsHex, 3, 4);
            $timeHigh = substr($tsHex, 0, 3);

            $hex = "{$timeLow}{$timeMid}1{$timeHigh}{$clockHex}{$nodeHex}";
        }

        return $hex;
    }
}
