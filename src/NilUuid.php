<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

final readonly class NilUuid extends Uuid
{
    public const BYTES = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";

    public function __construct()
    {
        parent::__construct(self::BYTES);
    }

    protected function assertValid(string $bytes): void
    {
        // noop
    }
}
