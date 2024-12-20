Parser
######

.. highlight:: php

``Arokettu\Uuid\UuidParser`` and ``Arokettu\Uuid\UlidParser``

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

``UuidParser`` autodetects UUID versions: nil, max, variant 10xx versions 1-8 as described in `RFC 9562`_.
If the version is not determined, an instance of ``Arokettu\Uuid\GenericUuid`` is created.
``UlidParser`` always gets ULID::

    <?php

    use Arokettu\Uuid\UlidParser;

    // same bytes as {6ba7b811-9dad-11d1-80b4-00c04fd430c8}
    // but it's ULID 3BMYW137DD278R1D00R17X8C68
    $bytes = "\x6b\xa7\xb8\x11\x9d\xad\x11\xd1\x80\xb4\x00\xc0\x4f\xd4\x30\xc8";

    $ulid = UlidParser::fromBytes($bytes, asUlid: true);
    var_dump($ulid->toString());    // 3BMYW137DD278R1D00R17X8C68
    var_dump($ulid::class);         // Arokettu\Uuid\Ulid
    var_dump($ulid->getDateTime()->format('c')); // 5720-12-08T01:31:12+00:00

.. warning::
    This method is for the natural/network big endian byte sequences.
    Use ``fromGuidBytes()`` for Microsoft GUID byte order.

fromGuidBytes()
===============

Like ``fromBytes()`` but the byte order is Microsoft GUID mixed-endian::

    <?php

    use Arokettu\Uuid\UuidParser;

    $guidBytes = '33221100554477668899aabbccddeeff';
    $guid = UuidParser::fromGuidBytes(hex2bin($guidBytes));
    var_dump($guid->toString()); // '00112233-4455-6677-8899-aabbccddeeff'

.. warning::
    Make sure you use it only on actual GUID ordered byte sequences.
    Like ``fromBytes()`` it will always succeed but you will get an incorrect UUID if it was in the natural (big endian) order.

fromRfcFormat()
===============

.. versionadded:: 3.0 strict mode.
.. versionchanged:: 3.0
    ``fromRfc4122`` -> ``fromRfcFormat``.
    ``fromRfc4122`` is kept as an alias.
    ``fromRfc9562`` added as an alias.

Parses `RFC notation`_ into UUID or ULID.

Supported formats:

* Standard hex: ``6ba7b811-9dad-11d1-80b4-00c04fd430c8``
* Standard hex with curly braces: ``{6ba7b811-9dad-11d1-80b4-00c04fd430c8}``
* Hex without dashes: ``6ba7b8119dad11d180b400c04fd430c8``
* Hex without dashes in curly braces: ``{6ba7b8119dad11d180b400c04fd430c8}``

Strict mode only supports the standard hex format.
All representations are case insensitive.
The type is determined the same way as in ``fromBytes()``.

::

    <?php

    use Arokettu\Uuid\UlidParser;
    use Arokettu\Uuid\UuidParser;

    $string = '{6ba7b811-9dad-11d1-80b4-00c04fd430c8}';

    $uuid = UuidParser::fromRfcFormat($string);
    var_dump($uuid->toString());    // 6ba7b811-9dad-11d1-80b4-00c04fd430c8
    var_dump($uuid::class);         // Arokettu\Uuid\UuidV1

    // curly braces are not allowed in the strict mode
    UuidParser::fromRfcFormat($string, strict: true); // UnexpectedValueException

    $ulid = UlidParser::fromRfcFormat($string);
    var_dump($ulid->toString());    // 3BMYW137DD278R1D00R17X8C68
    var_dump($ulid::class);         // Arokettu\Uuid\Ulid

fromBase32()
============

.. versionadded:: 2.5 strict mode

Parses Crockford's Base32 as defined in the `ULID spec`_.
The input is case insensitive.
Strict mode parser does not allow characters ``ILO``.
Non-strict mode parser interprets them as ``1`` or ``0`` as per Crockford's original standard.

::

    <?php

    use Arokettu\Uuid\UlidParser;
    use Arokettu\Uuid\UuidParser;

    $string = '3BMYW137DD278R1D00R17X8C68';

    $ulid = UlidParser::fromBase32($string);
    var_dump($ulid->toString());    // 3BMYW137DD278R1D00R17X8C68
    var_dump($ulid::class);         // Arokettu\Uuid\Ulid

    $uuid = UuidParser::fromBase32($string);
    var_dump($uuid->toString());    // 6ba7b811-9dad-11d1-80b4-00c04fd430c8
    var_dump($uuid::class);         // Arokettu\Uuid\UuidV1

fromString() / parse()
======================

.. versionadded:: 2.4 ``parse()``

``fromString()`` (alias ``parse()``) tries to use ``fromRfcFormat()`` and ``fromBase32()`` to parse the given string.

fromDecimal()
=============

Parses a decimal string that represents UUID as an unsigned 128-bit big-endian integer.

.. versionadded:: 2.1

::

    <?php

    use Arokettu\Uuid\UuidFactory;
    use Arokettu\Uuid\UuidParser;

    $uuid = UuidParser::fromDecimal('24197857203266357084698060135742627568');

    var_dump($uuid->toString()); // 12345678-9abc-8ef0-9234-56789abcdef0

.. _RFC 9562: https://datatracker.ietf.org/doc/html/rfc9562
.. _RFC notation: https://datatracker.ietf.org/doc/html/rfc9562#section-4
.. _ULID spec: https://github.com/ulid/spec
