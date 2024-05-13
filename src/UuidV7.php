<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

final readonly class UuidV7 extends AbstractUuid implements Variant10xxUuid, TimeBasedUuid
{
    use Helpers\Variant10xxUuidTrait;
    use Helpers\UlidLikeDateTime;

    public function getVersion(): int
    {
        return 7;
    }

    /**
     * @psalm-api
     */
    public function toUlid(): Ulid
    {
        return new Ulid($this->hex);
    }
}
