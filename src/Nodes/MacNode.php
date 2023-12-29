<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Nodes;

use Arokettu\Uuid\Helpers\NodeStringTrait;
use Arokettu\Uuid\Helpers\SystemMac;

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

        throw new \DomainException('Unrecognized MAC format');
    }

    public static function system(): self
    {
        return self::trySystem() ?? throw new \RuntimeException('Unable to determine system MAC address');
    }

    public static function trySystem(): ?self
    {
        $mac = SystemMac::get();
        // @codeCoverageIgnoreStart
        // we can't test success and failure in the same process
        if ($mac === '') {
            return null;
        }
        // @codeCoverageIgnoreEnd
        return self::parse($mac);
    }

    public function __debugInfo(): array
    {
        return ['mac' => $this->toString()];
    }
}
