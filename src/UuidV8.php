<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

readonly class UuidV8 extends AbstractUuid implements Rfc4122Uuid
{
    use Helpers\Rfc4122Variant1UUID {
        assertValid as baseAssertValid;
    }

    final public function __construct(string $bytes)
    {
        parent::__construct($bytes);
    }

    final protected function assertValid(string $bytes): void
    {
        $this->baseAssertValid($bytes);
        $this->customAssertValid($bytes);
    }

    protected function customAssertValid(string $bytes): void
    {
    }

    // do not allow overriding of toString()
    final public function toString(): string
    {
        return $this->toRfc4122();
    }

    final public function getRfc4122Version(): int
    {
        return 8;
    }
}
