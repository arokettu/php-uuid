<?php

declare(strict_types=1);

namespace Arokettu\Uuid\NodeID;

use Random\Engine\Secure;
use Random\Randomizer;

final class RandomNodeID implements NodeID
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
