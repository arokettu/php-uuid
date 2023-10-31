<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Nodes;

use Random\Engine\Secure;
use Random\Randomizer;

final class RandomNode implements Node
{
    public function __construct(
        private ?Randomizer $randomizer = null,
    ) {
        $this->randomizer ??= new Randomizer(new Secure());
    }

    public function getHex(): string
    {
        $random = $this->randomizer->getBytes(6) | "\x01"; // set multicast bit

        return bin2hex($random);
    }
}
