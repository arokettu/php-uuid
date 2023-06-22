<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

/**
 * @internal
 */
final class Constants
{
    public const V1_EPOCH = -12219292800; // (new DateTimeImmutable('1582-10-15 UTC'))->getTimestamp()
}
