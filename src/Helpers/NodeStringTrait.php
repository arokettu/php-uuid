<?php

/**
 * @copyright 2023 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

trait NodeStringTrait
{
    abstract public function getHex(): string;

    public function toString(): string
    {
        return implode(':', str_split($this->getHex(), 2));
    }
}
