<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

interface Uuid extends \Stringable
{
    /**
     * @psalm-api
     */
    public function toHex(): string;

    /**
     * @psalm-api
     */
    public function toBytes(): string;

    /**
     * @psalm-api
     */
    public function toGuidBytes(): string;

    /**
     * @psalm-api
     */
    public function toRfc4122(): string;

    /**
     * @psalm-api
     */
    public function toBase32(): string;

    /**
     * @psalm-api
     */
    public function toString(): string;

    /**
     * @psalm-api
     */
    public function equalTo(Uuid $uuid, bool $strict = true): bool;

    /**
     * @psalm-api
     */
    public function compare(Uuid $uuid): int;
}
