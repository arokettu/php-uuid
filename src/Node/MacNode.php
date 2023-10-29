<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Node;

use Arokettu\Uuid\Helpers\SystemMac;

final readonly class MacNode implements Node
{
    private function __construct(
        private string $hex,
    ) {
    }

    public function getHex(): string
    {
        return $this->hex;
    }

    public static function parse(string $mac): self
    {
        // MAC-48 / EUI-48 in Unix notation
        if (preg_match('/^[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}$/i', $mac)) {
            return new self(strtolower(str_replace(':', '', $mac)));
        }

        // MAC-48 / EUI-48 in Windows notation
        if (preg_match('/^[0-9a-f]{2}-[0-9a-f]{2}-[0-9a-f]{2}-[0-9a-f]{2}-[0-9a-f]{2}-[0-9a-f]{2}$/i', $mac)) {
            return new self(strtolower(str_replace('-', '', $mac)));
        }

        throw new \UnexpectedValueException('Unrecognized MAC format');
    }

    public static function system(): self
    {
        return self::parse(SystemMac::get());
    }
}
