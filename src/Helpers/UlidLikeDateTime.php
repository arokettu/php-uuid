<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

use DateTimeImmutable;

/**
 * @internal
 */
trait UlidLikeDateTime
{
    protected readonly string $hex;

    public function getDateTime(): DateTimeImmutable
    {
        return DateTime::parseUlidHex(substr($this->hex, 0, 12)); // first 48 bits are a timestamp
    }
}
