<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Sequences;

use Arokettu\Clock\SystemClock;
use Arokettu\Uuid\Helpers\DateTime;
use Arokettu\Uuid\Helpers\UuidBytes;
use Arokettu\Uuid\Helpers\UuidVariant;
use Arokettu\Uuid\Nodes;
use Arokettu\Uuid\UuidV6;
use DateInterval;
use DateTimeImmutable;
use Generator;
use IteratorAggregate;
use Psr\Clock\ClockInterface;
use Random\Engine\Secure;
use Random\Randomizer;

/**
 * @implements IteratorAggregate<int, UuidV6>
 */
final class UuidV6Sequence implements IteratorAggregate
{
    private const MAX_COUNTER = 0x3fff; // 14 bit
    private const MAX_NSEC100_COUNTER = 9; // one decimal

    private static DateInterval $ONE_MICROSECOND;

    private DateTimeImmutable $time;
    private int $nsec100Counter;
    private int $counter;

    public function __construct(
        private ?Nodes\Node $node = null,
        private ClockInterface $clock = new SystemClock(),
        private Randomizer $randomizer = new Randomizer(new Secure()),
    ) {
        $this->node ??= Nodes\StaticNode::random($this->randomizer);

        // init 'const' if not initialized
        self::$ONE_MICROSECOND ??= DateInterval::createFromDateString('1 microsecond');
    }

    public function next(): UuidV6
    {
        $time = $this->clock->now();

        // this will break if PHP ever goes beyond milliseconds
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
        $clockHex = str_pad(dechex($this->counter), 4, '0', STR_PAD_LEFT); // 2 bytes
        $nodeHex = $this->node->getHex();

        $hex =
            substr($tsHex, 0, 12) . // time_high + time_mid
            '0' . // version placeholder
            substr($tsHex, 12, 3) . // time_low
            $clockHex .
            $nodeHex;

        UuidBytes::setVariant($hex, UuidVariant::RFC4122);
        UuidBytes::setVersion($hex, 6);

        return new UuidV6($hex);
    }

    public function getIterator(): Generator
    {
        while (true) {
            yield $this->next();
        }
    }
}
