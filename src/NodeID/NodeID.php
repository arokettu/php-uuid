<?php

declare(strict_types=1);

namespace Arokettu\Uuid\NodeID;

interface NodeID
{
    public function getHex(): string;
}
