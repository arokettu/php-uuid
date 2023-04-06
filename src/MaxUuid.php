<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

final class MaxUuid extends Uuid
{
    public function __construct()
    {
        parent::__construct("\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff");
    }

    protected function assertValid(string $bytes): void
    {
        // noop
    }
}
