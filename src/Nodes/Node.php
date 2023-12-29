<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Nodes;

interface Node
{
    /**
     * @return string 12 lowercase hex digits
     */
    public function getHex(): string;

    /**
     * @return string Unix-style string representation
     */
    public function toString(): string;
}
