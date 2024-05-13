<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Namespaces;

use Arokettu\Uuid\Helpers\GenericParser;
use Arokettu\Uuid\Uuid;
use DomainException;

final readonly class CustomNamespace implements NamespaceInterface
{
    public function __construct(
        public string $bytes,
    ) {
    }

    public static function fromBytes(string $bytes): self
    {
        return new self($bytes);
    }

    public static function fromHex(string $hex): self
    {
        return new self(
            hex2bin($hex) ?: throw new DomainException('Invalid hexadecimal string: ' . $hex)
        );
    }

    public static function fromUuid(Uuid $uuid): self
    {
        return new self($uuid->toBytes());
    }

    public static function fromUuidString(string $uuid): self
    {
        return new self(GenericParser::fromString($uuid)->toBytes());
    }

    public function getBytes(): string
    {
        return $this->bytes;
    }
}
