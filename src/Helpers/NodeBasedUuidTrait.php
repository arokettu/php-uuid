<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

use Arokettu\Uuid\Nodes\RawNode;

trait NodeBasedUuidTrait
{
    protected readonly string $hex;

    public function getNode(): RawNode
    {
        return new RawNode(substr($this->hex, 20, 12));
    }

    public function getClockSequence(): int
    {
        $hex = substr($this->hex, 16, 4);

        return hexdec($hex) & 0x3fff; // cut out variant bits
    }
}
