<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use Arokettu\Uuid\Nodes\Node;

interface NodeBasedUuid
{
    public function getNode(): Node;
    public function getClockSequence(): int;
}
