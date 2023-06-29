<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

final readonly class UuidV7 extends AbstractUuid implements Rfc4122Uuid, TimeBasedUuid
{
    use Helpers\Rfc4122Variant1UUID;
    use Helpers\UlidLikeDateTime;

    public function getRfc4122Version(): int
    {
        return 7;
    }

    public function toUlid(): Ulid
    {
        return new Ulid($this->bytes);
    }
}
