<?php

namespace Arokettu\Uuid;

interface Uuid extends \Stringable
{
    public function toBytes(): string;

    public function toRfc4122(): string;

    public function toBase32(): string;

    public function toString(): string;

    public function equalTo(Uuid $uuid, bool $strict = true): bool;

    public function compare(Uuid $uuid): int;
}
