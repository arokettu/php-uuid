<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Nodes;

use Arokettu\Uuid\Helpers\NodeStringTrait;
use Arokettu\Uuid\Helpers\SystemMac;
use RuntimeException;
use UnexpectedValueException;

final readonly class MacNode implements Node
{
    use NodeStringTrait;

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

        // MAC-48 / EUI-48 in hex
        if (preg_match('/^[0-9a-f]{12}$/i', $mac)) {
            return new self(strtolower($mac));
        }

        throw new UnexpectedValueException('Unrecognized MAC format');
    }

    public static function system(): self
    {
        return self::trySystem() ?? throw new RuntimeException('Unable to determine system MAC address');
    }

    public static function trySystem(): self|null
    {
        $mac = SystemMac::get();
        if ($mac === '') {
            return null;
        }
        return self::parse($mac);
    }

    public function __debugInfo(): array
    {
        return ['mac' => $this->toString()];
    }
}
