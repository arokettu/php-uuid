<?php

/**
 * @copyright 2023 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

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
}
