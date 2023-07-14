<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

final readonly class UuidV7 extends AbstractUuid implements Rfc4122Variant10xxUuid, TimeBasedUuid
{
    use Helpers\Rfc4122Variant1UUID;
    use Helpers\UlidLikeDateTime;

    public function getRfc4122Version(): int
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
