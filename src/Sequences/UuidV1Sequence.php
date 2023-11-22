<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Sequences;

use Arokettu\Clock\SystemClock;
use Arokettu\Uuid\Helpers\DateTime;
use Arokettu\Uuid\Helpers\UuidBytes;
use Arokettu\Uuid\Helpers\UuidVariant;
use Arokettu\Uuid\Nodes;
use Arokettu\Uuid\UuidV1;
use DateInterval;
use DateTimeImmutable;
use Generator;
use Psr\Clock\ClockInterface;
use Random\Engine\Secure;
use Random\Randomizer;

/**
 * @implements UuidSequence<UuidV1>
 */
final class UuidV1Sequence implements UuidSequence
{
    private const MAX_COUNTER = 0x3fff; // 14 bit
    private const MAX_NSEC100_COUNTER = 9; // one decimal

    private static DateInterval $ONE_MICROSECOND;

    private readonly Nodes\Node $node;

    private DateTimeImmutable $time;
    private int $nsec100Counter;
    private int $counter;

    public function __construct(
        ?Nodes\Node $node = null,
        private readonly ClockInterface $clock = new SystemClock(),
        private readonly Randomizer $randomizer = new Randomizer(new Secure()),
    ) {
        $this->node = $node ?? Nodes\StaticNode::random($this->randomizer);

        // init 'const' if not initialized
        self::$ONE_MICROSECOND ??= DateInterval::createFromDateString('1usec');
    }

    public function next(): UuidV1
    {
        $time = $this->clock->now();

        if (!isset($this->time) || $this->time < $time) {
            // if time advanced, reset everything
            $this->time = $time;
            $this->nsec100Counter = 0;
            $this->counter = $this->randomizer->getInt(0, self::MAX_COUNTER);
        } else {
            $this->counter += 1;

            if ($this->counter > self::MAX_COUNTER) {
                $this->nsec100Counter += 1;
                $this->counter = $this->randomizer->getInt(0, self::MAX_COUNTER);

                if ($this->nsec100Counter > self::MAX_NSEC100_COUNTER) {
                    $this->time = $this->time->add(self::$ONE_MICROSECOND);
                    $this->nsec100Counter = 0;
                }
            }
        }

        $tsHex = DateTime::buildUuidV1Hex($this->time, $this->nsec100Counter);
        $clockHex = sprintf('%04x', $this->counter); // 2 bytes
        $nodeHex = $this->node->getHex();

        $timeLow  = substr($tsHex, 7, 8);
        $timeMid  = substr($tsHex, 3, 4);
        $timeHigh = substr($tsHex, 0, 3);

        $hex = "{$timeLow}{$timeMid}1{$timeHigh}{$clockHex}{$nodeHex}";

        UuidBytes::setVariant($hex, UuidVariant::RFC4122);

        return new UuidV1($hex);
    }

    public function getIterator(): Generator
    {
        while (true) {
            yield $this->next();
        }
    }
}
