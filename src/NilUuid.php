<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

final class NilUuid extends BaseUuid
{
    public function __construct()
    {
        parent::__construct("\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
    }

    protected function assertValid(string $bytes): void
    {
        // noop
    }
}
