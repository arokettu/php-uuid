<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Namespaces;

use Arokettu\Uuid\UuidV1;

/**
 * @psalm-api
 */
enum UuidNamespace implements NamespaceInterface
{
    case DNS;
    case URL;
    case OID;
    case X500;

    public function getHex(): string
    {
        return match ($this) {
            self::DNS   => '6ba7b8109dad11d180b400c04fd430c8', // 6ba7b810-9dad-11d1-80b4-00c04fd430c8
            self::URL   => '6ba7b8119dad11d180b400c04fd430c8', // 6ba7b811-9dad-11d1-80b4-00c04fd430c8
            self::OID   => '6ba7b8129dad11d180b400c04fd430c8', // 6ba7b812-9dad-11d1-80b4-00c04fd430c8
            self::X500  => '6ba7b8149dad11d180b400c04fd430c8', // 6ba7b814-9dad-11d1-80b4-00c04fd430c8
        };
    }

    public function getUuid(): UuidV1
    {
        return new UuidV1($this->getHex());
    }

    public function getBytes(): string
    {
        return hex2bin($this->getHex());
    }
}
