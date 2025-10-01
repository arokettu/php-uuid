<?php

/**
 * @copyright 2023 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Uuid;

final readonly class NilUuid extends AbstractUuid implements Rfc4122Uuid, Rfc9562Uuid
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
