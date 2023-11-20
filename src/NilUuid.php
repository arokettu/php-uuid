<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

final readonly class NilUuid extends AbstractUuid implements Rfc4122Uuid
{
    public const HEX = '00000000000000000000000000000000';

    public function __construct()
    {
        parent::__construct(self::HEX);
    }

    protected function assertValid(string $hex): void
    {
        // noop
    }
}
