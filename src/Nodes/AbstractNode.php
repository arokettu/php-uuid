<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Nodes;

use Arokettu\Uuid\Helpers\NodeStringTrait;
use DomainException;
use UnexpectedValueException;

abstract readonly class AbstractNode implements Node
{
    use NodeStringTrait;

    abstract protected function assertValid(string $hex): void;

    final public function __construct(
        protected string $hex,
    ) {
        if (preg_match('/^[0-9a-f]{12}$/', $this->hex) !== 1) {
            throw new DomainException('$hex must be 12 lowercase hexadecimal digits');
        }

        $this->assertValid($this->hex);
    }

    protected static function normalize(string $hex): string
    {
        return $hex;
    }

    final public static function fromHex(string $hex): static
    {
        if (preg_match('/^[0-9a-f]{12}$/i', $hex) !== 1) {
            throw new UnexpectedValueException('$hex must be 12 hexadecimal digits');
        }

        return new static(static::normalize(strtolower($hex)));
    }

    final public static function fromBytes(string $bytes): static
    {
        if (\strlen($bytes) !== 6) {
            throw new DomainException('$bytes must be 6 bytes');
        }

        return new static(static::normalize(bin2hex($bytes)));
    }

    final public function getHex(): string
    {
        return $this->hex;
    }

    final public function __debugInfo(): array
    {
        return ['mac' => $this->toString()];
    }
}
