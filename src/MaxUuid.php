<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

final readonly class MaxUuid extends AbstractUuid
{
    public const BYTES = "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff";

    public function __construct()
    {
        parent::__construct(self::BYTES);
    }

    protected function assertValid(string $bytes): void
    {
        // noop
    }
}
