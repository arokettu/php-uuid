<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Nodes;

use Random\Engine\Secure;
use Random\Randomizer;

final readonly class StaticNode extends AbstractNode
{
    protected function assertValid(string $hex): void
    {
        if ((hexdec($hex[1]) & 1) === 0) {
            throw new \UnexpectedValueException('The lowest bit of the first byte must be set for non-MAC nodes');
        }
    }

    protected static function normalize(string $hex): string
    {
        $hex[1] = dechex(hexdec($hex[1]) | 1);
        return $hex;
    }

    public static function random(?Randomizer $randomizer = null): self
    {
        $randomizer ??= new Randomizer(new Secure());
        return self::fromBytes($randomizer->getBytes(6));
    }
}
