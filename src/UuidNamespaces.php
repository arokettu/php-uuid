<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

final class UuidNamespaces
{
    public const DNS    = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';
    public const URL    = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';
    public const OID    = '6ba7b812-9dad-11d1-80b4-00c04fd430c8';
    public const X500   = '6ba7b814-9dad-11d1-80b4-00c04fd430c8';

    private const DNS_BIN   = "\x6b\xa7\xb8\x10\x9d\xad\x11\xd1\x80\xb4\x00\xc0\x4f\xd4\x30\xc8";
    private const URL_BIN   = "\x6b\xa7\xb8\x11\x9d\xad\x11\xd1\x80\xb4\x00\xc0\x4f\xd4\x30\xc8";
    private const OID_BIN   = "\x6b\xa7\xb8\x12\x9d\xad\x11\xd1\x80\xb4\x00\xc0\x4f\xd4\x30\xc8";
    private const X500_BIN  = "\x6b\xa7\xb8\x14\x9d\xad\x11\xd1\x80\xb4\x00\xc0\x4f\xd4\x30\xc8";

    public static function dns(): UuidV1
    {
        return new UuidV1(self::DNS_BIN);
    }

    public static function url(): UuidV1
    {
        return new UuidV1(self::URL_BIN);
    }

    public static function oid(): UuidV1
    {
        return new UuidV1(self::OID_BIN);
    }

    public static function x500(): UuidV1
    {
        return new UuidV1(self::X500_BIN);
    }
}
