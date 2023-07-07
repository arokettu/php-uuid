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

.. note:: ``UuidParser::fromRfc4122()`` and ``UuidParser::fromRfc4122()`` can parse UUIDs/ULIDs in hex in case-insensitive manner.

Subclasses may also check the string for additional validity.

Conversion to String
--------------------

Methods to convert UUID object to string:

* ``toRfc4122()`` to `RFC 4122`_ form. Example: ``"6ba7b811-9dad-11d1-80b4-00c04fd430c8"``
* ``toBase32()`` to Base32. Example: ``"3BMYW137DD278R1D00R17X8C68"``
* ``toString()``. Converts UUIDs to RFC 4122 and ULIDs to Base32.

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

Rfc4122Uuid
===========

`RFC 4122`_ UUID versions (except for Nil and Max) extend this interface.
This interface is most useful to check that it is a standard based UUID as opposed to Nil, Max, ULID or unrecognized generic.

::

    <?php

    use Arokettu\Uuid\Rfc4122Uuid;
    use Arokettu\Uuid\UlidFactory;
    use Arokettu\Uuid\UuidFactory;

    $uuid = UuidFactory::v4();
    var_dump($uuid instanceof Rfc4122Uuid); // true
    var_dump($uuid->getRfc4122Version()); // 4

    $ulid = UlidFactory::ulid();
    var_dump($ulid instanceof Rfc4122Uuid); // false

TimeBasedUuid
=============

UUIDv1, UUIDv2, UUIDv6, UUIDv7, and ULID extend this interface because they encode timestamp with various precisions::

    <?php

    use Arokettu\Uuid\UuidFactory;

    $uuid = UuidFactory::v7();
    var_dump($uuid->getDateTime()->format('c')); // current time

.. _RFC 4122: https://datatracker.ietf.org/doc/html/rfc4122
.. _strcmp: https://www.php.net/manual/en/function.strcmp.php
