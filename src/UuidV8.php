<?php

/**
 * @copyright 2023 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Uuid;

abstract readonly class UuidV8 extends AbstractUuid implements Variant10xxUuid, Rfc9562Uuid
{
    use Helpers\Variant10xxUuidTrait {
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
        // for overrides
    }

    // do not allow overriding of toString()
    final public function toString(): string
    {
        return $this->toRfcFormat();
    }

    final public function getVersion(): int
    {
        return 8;
    }
}
