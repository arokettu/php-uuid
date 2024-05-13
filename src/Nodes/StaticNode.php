<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Nodes;

use DomainException;
use Random\Engine\PcgOneseq128XslRr64;
use Random\Randomizer;

final readonly class StaticNode extends AbstractNode
{
    protected function assertValid(string $hex): void
    {
        if ((hexdec($hex[1]) & 1) === 0) {
            throw new DomainException('The lowest bit of the first byte must be set for non-MAC nodes');
        }
    }

    protected static function normalize(string $hex): string
    {
        $hex[1] = dechex(hexdec($hex[1]) | 1);
        return $hex;
    }

    public static function random(Randomizer $randomizer = new Randomizer(new PcgOneseq128XslRr64())): self
    {
        return self::fromBytes($randomizer->getBytes(6));
    }
}
