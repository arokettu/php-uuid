<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

readonly class UuidV8 extends AbstractUuid implements Rfc4122Variant10xxUuid
{
    use Helpers\Rfc4122Variant10xxUUID {
        assertValid as baseAssertValid;
    }

    final public function __construct(string $hex)
    {
        parent::__construct($hex);
    }

    final protected function assertValid(string $hex): void
    {
        $this->baseAssertValid($hex);
        $this->customAssertValid($hex);
    }

    /**
     * @psalm-suppress PossiblyUnusedParam
     */
    protected function customAssertValid(string $hex): void
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
