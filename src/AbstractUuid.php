<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use DomainException;

use function Arokettu\Unsigned\to_dec;

abstract readonly class AbstractUuid implements Uuid
{
    public function __construct(
        protected string $hex,
    ) {
        if (preg_match('/^[0-9a-f]{32}$/', $this->hex) !== 1) {
            throw new DomainException('$hex must be 32 lowercase hexadecimal digits');
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

    final public function toGuidBytes(): string
    {
        $bytes = hex2bin($this->hex);

        $seg1 = substr($bytes, 0, 4);
        $seg2 = substr($bytes, 4, 2);
        $seg3 = substr($bytes, 6, 2);
        $seg4 = substr($bytes, 8);

        return strrev($seg1) . strrev($seg2) . strrev($seg3) . $seg4;
    }

    final public function toRfc4122(): string
    {
        $seg1 = substr($this->hex, 0, 8);
        $seg2 = substr($this->hex, 8, 4);
        $seg3 = substr($this->hex, 12, 4);
        $seg4 = substr($this->hex, 16, 4);
        $seg5 = substr($this->hex, 20);

        return "$seg1-$seg2-$seg3-$seg4-$seg5";
    }

    final public function toBase32(): string
    {
        return Helpers\Base32::encode($this->hex);
    }

    final public function toDecimal(): string
    {
        return to_dec(strrev(hex2bin($this->hex)));
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
        return strcmp($this->hex, $uuid->toHex()) <=> 0;
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

    public function __debugInfo(): array
    {
        $data = [];

        if ($this instanceof Rfc4122Variant10xxUuid) {
            $data['version'] = $this->getRfc4122Version();
        }

        $data['rfc4122'] = $this->toRfc4122();
        $data['base32'] = $this->toBase32();

        if ($this instanceof TimeBasedUuid) {
            $data['timestamp'] = $this->getDateTime()->format(
                'Y-m-d\TH:i:s.uP' // like DATE_RFC3339_EXTENDED but with microseconds
            );
        }

        return $data;
    }
}
