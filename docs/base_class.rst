Base Class and Interfaces
#########################

.. highlight:: php

Base class
==========

``Arokettu\Uuid\AbstractUuid`` class and ``Arokettu\Uuid\Uuid`` interface.

Direct creation
---------------

The base constructor is inherited by most of the descendants.
It accepts a string of lowercase hexadecimal digits (0-9, a-f)::

    <?php

    use Arokettu\Uuid\GenericUuid;

    // {12345678-9abc-def0-1234-56789abcdef0}
    $uuid = new GenericUuid('123456789abcdef0123456789abcdef0');

.. note:: ``UuidParser::fromRfcFormat()`` and ``UlidParser::fromRfcFormat()`` can parse UUIDs/ULIDs in hex in case-insensitive manner.

Subclasses may also check the string for additional validity.

Conversion to Bytes
-------------------

.. versionadded:: 1.2 ``toGuidBytes()``

Methods to convert UUID object to a byte sequence:

* ``toBytes()`` to the raw big-endian byte sequence
* ``toGuidBytes()`` to the Microsoft GUID mixed-endian byte sequence

Conversion to String
--------------------

.. versionadded:: 2.1 ``toDecimal()``

Methods to convert UUID object to a readable string:

* ``toString()``. Converts UUIDs to the RFC Format and ULIDs to Base32.
* ``toRfcFormat()`` to `RFC 9562`_ form.
  Aliases: ``toRfc4122()``, ``toRfc9562()``.
  Example: ``"6ba7b811-9dad-11d1-80b4-00c04fd430c8"``
* ``toBase32()`` to Base32. Example: ``"3BMYW137DD278R1D00R17X8C68"``
* ``toDecimal()`` to decimal. Example: ``"143098242483405524118141958906375844040"``

.. note::

    ``toDecimal()`` can be used to create OID representation of the UUID::

        <?php

        // add OID UUID prefix: 2.25
        $oid = '2.25.' . $uuid->toDecimal(); // 2.25.143098242483405524118141958906375844040

Comparison
----------

Methods to compare two UUID objects.

* ``compare(): int``.
  Returns same thing as strcmp_.
* ``equalTo(): bool``.
  In strict mode (by default) returns true if bytes and types of the two objects are equal.
  In non-strict mode compares only byte values.

::

    <?php

    use Arokettu\Uuid\UlidParser;
    use Arokettu\Uuid\UuidParser;

    $uuid = UuidParser::fromString('6ba7b811-9dad-11d1-80b4-00c04fd430c8');
    $ulid = UlidParser::fromString('3BMYW137DD278R1D00R17X8C68');

    var_dump($uuid->compare($ulid)); // 0
    var_dump($uuid->equalTo($ulid)); // false
    var_dump($uuid->equalTo($ulid, strict: false)); // true

Variant10xxUuid
===============

.. versionadded:: 1.1
.. versionchanged:: 1.2 renamed from Rfc4122Variant1Uuid to Rfc4122Variant10xxUuid
.. versionchanged:: 3.0 renamed from Rfc4122Variant10xxUuid to Variant10xxUuid

`RFC 9562`_ Variant 10xx UUID versions (all except for Nil and Max) extend this interface.
This interface is most useful to check that it is a standard based UUID as opposed to Nil, Max, ULID or unrecognized generic.

::

    <?php

    use Arokettu\Uuid\UlidFactory;
    use Arokettu\Uuid\UuidFactory;
    use Arokettu\Uuid\Variant10xxUuid;

    $uuid = UuidFactory::v4();
    var_dump($uuid instanceof Variant10xxUuid); // true
    var_dump($uuid->getVersion()); // 4

    $ulid = UlidFactory::ulid();
    var_dump($ulid instanceof Variant10xxUuid); // false

Rfc4122Uuid
===========

.. versionchanged:: 1.1 Now includes Nil and Max
.. versionchanged:: 3.0 No longer contains Max, UUIDv2, UUIDv6, UUIDv7, UUIDv8

All UUIDs mentioned in `RFC 4122`_, i.e. Nil, and Variant10xxUuid versions 1-5 excluding 2.

Rfc9562Uuid
===========

.. versionadded:: 3.0

All UUIDs mentioned in `RFC 9562`_, i.e. Nil, Max, Variant10xxUuid versions 1-8 excluding 2.

TimeBasedUuid
=============

UUIDv1, UUIDv2, UUIDv6, UUIDv7, and ULID extend this interface because they encode timestamp with various precisions::

    <?php

    use Arokettu\Uuid\UuidFactory;

    $uuid = UuidFactory::v7();
    var_dump($uuid->getDateTime()->format('c')); // current time

.. _RFC 4122: https://datatracker.ietf.org/doc/html/rfc4122
.. _RFC 9562: https://datatracker.ietf.org/doc/html/rfc9562
.. _strcmp: https://www.php.net/manual/en/function.strcmp.php

NodeBasedUuid
=============

.. versionadded:: 4.0

UUIDv1, UUIDv2, and UUIDv6 extend this interface because they are based on a Node and a Clock Sequence::

    <?php

    use Arokettu\Uuid\UuidFactory;

    $uuid = UuidFactory::v6();
    var_dump($uuid->getNode()); // Node value (MAC address or MAC-like pseudo-value)
    var_dump($uuid->getClockSequence()); // Clock Sequence value
