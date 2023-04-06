<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

abstract class Uuid
{
    public function __construct(
        protected readonly string $bytes,
    ) {
        if (\strlen($this->bytes) !== 16) {
            throw new \ValueError('$bytes must be 16 bytes long');
        }

        $this->assertValid($this->bytes);
    }

    abstract protected function assertValid(string $bytes): void;

    final public function toBytes(): string
    {
        return $this->bytes;
    }

    final public function toRfc4122(): string
    {
        return
            bin2hex(substr($this->bytes, 0, 4)) . '-' .
            bin2hex(substr($this->bytes, 4, 2)) . '-' .
            bin2hex(substr($this->bytes, 6, 2)) . '-' .
            bin2hex(substr($this->bytes, 8, 2)) . '-' .
            bin2hex(substr($this->bytes, 10));
    }

    final public function toBase32(): string
    {
        return Helpers\Base32::encode($this->bytes);
    }

    public function toString(): string
    {
        return $this->toRfc4122();
    }

    final public function __toString(): string
    {
        return $this->toString();
    }

    final public function __serialize(): array
    {
        return [$this->bytes];
    }

    final public function __unserialize(array $data): void
    {
        [$this->bytes] = $data;
    }
}
