<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

/**
 * @internal
 */
enum UuidVariant
{
    case v0xxx; // NCS backward compatibility
    case v10xx; // RFC 4122
    case v110x; // Microsoft Corporation backward compatibility
    case v111x; // Reserved for future definition

    public const RFC4122 = self::v10xx;
}
