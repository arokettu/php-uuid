<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

abstract class Uuid
{
    final public function __construct(
        protected readonly string $bytes,
    ) {
        if (\strlen($this->bytes) !== 16) {
            throw new \ValueError('$bytes must be 16 bytes long');
        }

        $this->assertValid($this->bytes);
    }

    abstract protected function assertValid(string $bytes): void;

    final public function toRfc4122(): string
    {
        return
            bin2hex(substr($this->bytes, 0, 4)) . '-' .
            bin2hex(substr($this->bytes, 4, 2)) . '-' .
            bin2hex(substr($this->bytes, 6, 2)) . '-' .
            bin2hex(substr($this->bytes, 8, 2)) . '-' .
            bin2hex(substr($this->bytes, 10));
    }
}
