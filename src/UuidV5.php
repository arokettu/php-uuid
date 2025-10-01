<?php

/**
 * @copyright 2023 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Uuid;

final readonly class UuidV5 extends AbstractUuid implements Variant10xxUuid, Rfc4122Uuid, Rfc9562Uuid
{
    use Helpers\Variant10xxUuidTrait;

    public function getVersion(): int
    {
        return 5;
    }
}
