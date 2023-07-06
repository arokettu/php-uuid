<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

abstract readonly class AbstractUuid implements Uuid
{
    public function __construct(
        protected string $hex,
    ) {
        if (preg_match('/^[0-9a-f]{32}$/', $this->hex) !== 1) {
            throw new \ValueError('$bytes must be 32 lowercase hexadecimal digits');
        }

        $this->assertValid($this->hex);
    }

    abstract protected function assertValid(string $hex): void;

    final public function toHex(): string
    {
        return $this->hex;
    }

    final public function toBytes(): string
    {
        return hex2bin($this->hex);
    }

    final public function toRfc4122(): string
    {
        return
            substr($this->hex, 0, 8) . '-' .
            substr($this->hex, 8, 4) . '-' .
            substr($this->hex, 12, 4) . '-' .
            substr($this->hex, 16, 4) . '-' .
            substr($this->hex, 20);
    }

    final public function toBase32(): string
    {
        return Helpers\Base32::encode($this->hex);
    }

    public function toString(): string
    {
        return $this->toRfc4122();
    }

    final public function equalTo(Uuid $uuid, bool $strict = true): bool
    {
        if ($strict && $this::class !== $uuid::class) {
            return false;
        }

        return $this->hex === $uuid->toHex();
    }

    final public function compare(Uuid $uuid): int
    {
        $compare = strcmp($this->hex, $uuid->toHex());
        return match (true) {
            $compare === 0
                => 0,
            $compare > 0
                => 1,
            $compare < 0
                => -1,
        };
    }

    final public function __toString(): string
    {
        return $this->toString();
    }

    final public function __serialize(): array
    {
        return [$this->hex];
    }

    final public function __unserialize(array $data): void
    {
        [$this->hex] = $data;
    }
}
