<?php

declare(strict_types=1);

namespace Arokettu\Uuid\NonStandard;

use Arokettu\Uuid\Uuid;
use Arokettu\Uuid\UuidFactory;
use Arokettu\Uuid\UuidV8;

final class CustomUuidFactory
{
    public static function sha256(Uuid $namespace, string $identifier): UuidV8
    {
        $bytes = hash('sha256', $namespace->toBytes() . $identifier, true);
        return UuidFactory::v8(substr($bytes, 0, 16));
    }
}
