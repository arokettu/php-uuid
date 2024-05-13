<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Nodes;

use Arokettu\Uuid\Helpers\NodeStringTrait;
use Random\Engine\PcgOneseq128XslRr64;
use Random\Randomizer;

final class RandomNode implements Node
{
    use NodeStringTrait;

    public function __construct(
        private Randomizer $randomizer = new Randomizer(new PcgOneseq128XslRr64()),
    ) {
    }

    public function getHex(): string
    {
        $random = $this->randomizer->getBytes(6) | "\x01"; // set multicast bit

        return bin2hex($random);
    }
}
