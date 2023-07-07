<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

/**
 * @psalm-api
 */
final class UuidNamespaces
{
    public const DNS    = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';
    public const URL    = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';
    public const OID    = '6ba7b812-9dad-11d1-80b4-00c04fd430c8';
    public const X500   = '6ba7b814-9dad-11d1-80b4-00c04fd430c8';

    private const DNS_HEX   = "6ba7b8109dad11d180b400c04fd430c8";
    private const URL_HEX   = "6ba7b8119dad11d180b400c04fd430c8";
    private const OID_HEX   = "6ba7b8129dad11d180b400c04fd430c8";
    private const X500_HEX  = "6ba7b8149dad11d180b400c04fd430c8";

    public static function dns(): UuidV1
    {
        return new UuidV1(self::DNS_HEX);
    }

    public static function url(): UuidV1
    {
        return new UuidV1(self::URL_HEX);
    }

    public static function oid(): UuidV1
    {
        return new UuidV1(self::OID_HEX);
    }

    public static function x500(): UuidV1
    {
        return new UuidV1(self::X500_HEX);
    }
}
