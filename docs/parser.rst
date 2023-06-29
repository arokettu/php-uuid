Parser
######

.. highlight:: php

``Arokettu\Uuid\UuidParser``

A class to parse existing UUID or ULID.

fromBytes()
===========

Parses 16 bytes into UUID::

    <?php

    use Arokettu\Uuid\UuidParser;

    // {6ba7b811-9dad-11d1-80b4-00c04fd430c8}
    $bytes = "\x6b\xa7\xb8\x11\x9d\xad\x11\xd1\x80\xb4\x00\xc0\x4f\xd4\x30\xc8";

    $uuid = UuidParser::fromBytes($bytes);
    var_dump($uuid->toString());    // 6ba7b811-9dad-11d1-80b4-00c04fd430c8
    var_dump($uuid::class);         // Arokettu\Uuid\UuidV1
    var_dump($uuid->getDateTime()->format('c')); // 1998-02-04T22:13:53+00:00

The parser autodetects UUID versions: nil, max, variant 1 versions 1-8 as described in `RFC 4122`_.
If the version is not determined, an instance of ``Arokettu\Uuid\GenericUuid`` is created.
If you want ULID, you should specify it explicitly::

    <?php

    use Arokettu\Uuid\UuidParser;

    // same bytes as {6ba7b811-9dad-11d1-80b4-00c04fd430c8}
    // but it's ULID 3BMYW137DD278R1D00R17X8C68
    $bytes = "\x6b\xa7\xb8\x11\x9d\xad\x11\xd1\x80\xb4\x00\xc0\x4f\xd4\x30\xc8";

    $ulid = UuidParser::fromBytes($bytes, asUlid: true);
    var_dump($ulid->toString());    // 3BMYW137DD278R1D00R17X8C68
    var_dump($ulid::class);         // Arokettu\Uuid\Ulid
    var_dump($ulid->getDateTime()->format('c')); // 5720-12-08T01:31:12+00:00

fromRfc4122()
=============

Parses `RFC 4122`_ notation into UUID.

Supported formats:

* Standard hex: ``6ba7b811-9dad-11d1-80b4-00c04fd430c8``
* Standard hex with curly braces: ``{6ba7b811-9dad-11d1-80b4-00c04fd430c8}``
* Hex without dashes: ``6ba7b8119dad11d180b400c04fd430c8``
* Hex without dashes in curly braces: ``{6ba7b8119dad11d180b400c04fd430c8}``

All representations are case insensitive.
The type is determined the same way as in ``fromBytes()``.

::

    <?php

    use Arokettu\Uuid\UuidParser;

    $string = '{6ba7b811-9dad-11d1-80b4-00c04fd430c8}';

    $uuid = UuidParser::fromRfc4122($string);
    var_dump($uuid->toString());    // 6ba7b811-9dad-11d1-80b4-00c04fd430c8
    var_dump($uuid::class);         // Arokettu\Uuid\UuidV1

    $ulid = UuidParser::fromRfc4122($string, asUlid: true);
    var_dump($ulid->toString());    // 3BMYW137DD278R1D00R17X8C68
    var_dump($ulid::class);         // Arokettu\Uuid\Ulid

fromBase32()
============

Parses Crockford's Base32 as defined in the `ULID spec`_ into ULID.
The input is case insensitive.

You can force it to be parsed as UUID by passing ``asUuid: true``.

::

    <?php

    use Arokettu\Uuid\UuidParser;

    $string = '3BMYW137DD278R1D00R17X8C68';

    $ulid = UuidParser::fromBase32($string);
    var_dump($ulid->toString());    // 3BMYW137DD278R1D00R17X8C68
    var_dump($ulid::class);         // Arokettu\Uuid\Ulid

    $uuid = UuidParser::fromBase32($string, asUuid: true);
    var_dump($uuid->toString());    // 6ba7b811-9dad-11d1-80b4-00c04fd430c8
    var_dump($uuid::class);         // Arokettu\Uuid\UuidV1

fromString()
============

``fromString()`` tries to use ``fromRfc4122()`` and ``fromBase32()`` to parse the given string.
Currently it doesn't allow you to force UUID or ULID as a result
so RFC 4122 strings become UUIDs and Base32 strings become ULIDs.

.. _RFC 4122: https://datatracker.ietf.org/doc/html/rfc4122
.. _ULID spec: https://github.com/ulid/spec
