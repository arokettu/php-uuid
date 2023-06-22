<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

final class UuidV7 extends Uuid implements TimeBasedUuid
{
    use Helpers\Rfc4122Variant1UUID;
    use Helpers\UlidLikeDateTime;

    private function version(): int
    {
        return 7;
    }

    public function toUlid(): Ulid
    {
        return new Ulid($this->bytes);
    }
}
