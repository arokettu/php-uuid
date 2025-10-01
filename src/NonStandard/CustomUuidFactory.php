<?php

/**
 * @copyright 2023 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Uuid\NonStandard;

use Arokettu\Uuid\Namespaces\UuidNamespace;
use Arokettu\Uuid\Uuid;
use Arokettu\Uuid\UuidFactory;
use Arokettu\Uuid\UuidV8;

final class CustomUuidFactory
{
    public static function sha256(Uuid|UuidNamespace $namespace, string $identifier): UuidV8
    {
        $bytes = $namespace instanceof Uuid ? $namespace->toBytes() : $namespace->getBytes();
        $uuid = hash('sha256', $bytes . $identifier, true);
        return UuidFactory::v8(substr($uuid, 0, 16));
    }
}
