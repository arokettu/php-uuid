UUID Versions
#############

.. highlight:: php

Special
=======

Nil UUID
--------

``Arokettu\Uuid\NilUuid``

``{00000000-0000-0000-0000-000000000000}``

Nil UUID as described in `RFC 4122`_.
The class can be constructed directly, its constructor does not accept any parameters.

Max UUID
--------

``Arokettu\Uuid\MaxUuid``

``{ffffffff-ffff-ffff-ffff-ffffffffffff}``

Max UUID as described in `RFC 4122`_.
The class can be constructed directly, its constructor does not accept any parameters.

Generic UUID
------------

``Arokettu\Uuid\GenericUuid``

Any UUID that is not Nil, Max or RFC 4122 and is not marked as ULID will be parsed to this class.

The class can also be initialized directly with any 16 bytes of data.
(Obviously, the class will not be cast to a version class in case the data happen to be a valid version or special UUID)

RFC 4122
========

Variant 1 versions described in the `RFC 4122`_ and in the `update draft <RFC 4122 draft_>`__.

Any class can be initialized directly by 16 bytes of data but the correct variant and version bits must be set.

Version 1
---------

``Arokettu\Uuid\UuidV1``.

The library does not support generation of these UUIDs since they are more or less legacy stuff.

The class implements ``TimeBasedUuid`` interface.
UUIDv1 timestamp is measured to 100 nsec precision which is more than DateTime can handle
therefore the returned value will be truncated by one decimal.

UUIDv1 can be rearranged without loss of any data and precision into a lexicographically monotonic UUIDv6.
Use the ``toUuidV6()`` method for that::

    <?php

    use Arokettu\Uuid\UuidParser;

    $uuid1 = UuidParser::fromString('6ba7b811-9dad-11d1-80b4-00c04fd430c8');
    $uuid6 = $uuid1->toUuidV6();
    var_dump($uuid6->toString()); // 1d19dad6-ba7b-6811-80b4-00c04fd430c8

Version 2
---------

``Arokettu\Uuid\UuidV2``.

"DCE security" legacy UUID.
The library does not support generation of these UUIDs and preferably they should never be used.

The class implements ``TimeBasedUuid`` interface with 429'496'729'600 nsec precision (approximately 7 minutes)
due to truncated timestamp field compared to V1.

Version 3
---------

``Arokettu\Uuid\UuidV3``.

MD5 based namespace UUID.

Version 4
---------

``Arokettu\Uuid\UuidV4``.

Random UUID.
This is the recommended version if you don't need monotonicity.

Version 5
---------

``Arokettu\Uuid\UuidV5``.

SHA1 based namespace UUID.

Version 6
---------

``Arokettu\Uuid\UuidV6``.

Basically a rearrangement of UUIDv1 fields.
The library does not support generation of these UUIDs since they are more or less legacy compatibility stuff.
They are mostly useful as a conversion from UUIDv1.

The class implements ``TimeBasedUuid`` interface.
UUIDv1 timestamp is measured to 100 nsec precision which is more than DateTime can handle
therefore the returned value will be truncated by one decimal.

UUIDv6 can be rearranged without loss of any data and precision into a legacy UUIDv1.
Use the ``toUuidV1()`` method for that::

    <?php

    use Arokettu\Uuid\UuidParser;

    $uuid6 = UuidParser::fromString('1d19dad6-ba7b-6811-80b4-00c04fd430c8');
    $uuid1 = $uuid6->toUuidV1();
    var_dump($uuid1->toString()); // 6ba7b811-9dad-11d1-80b4-00c04fd430c8

Version 7
---------

``Arokettu\Uuid\UuidV7``.

A lexicographically monotonic version.
This is the recommended version if you do need monotonicity.

UUIDv7 was designed after ULID and shares the timestamp structure with it.

The class implements ``TimeBasedUuid`` interface with millisecond precision.

UUIDv7 without any bit change can be converted to a ULID.
Use ``toUlid()`` for that::

    <?php

    use Arokettu\Uuid\UuidParser;

    $uuid = UuidParser::fromString('01890974-6a48-7580-b4c2-bf9acde79240');
    $ulid = $uuid->toUlid();
    var_dump($ulid->toString());    // 01H44Q8TJ8EP0B9GNZKB6YF4J0
    var_dump($ulid->toRfc4122());   // 01890974-6a48-7580-b4c2-bf9acde79240

Version 8
---------

``Arokettu\Uuid\UuidV8``.

This is a special version for custom UUIDs.
The class can be extended::

    <?php

    readonly class UuidExtended extends UuidV8
    {
        protected function customAssertValid(string $bytes): void
        {
            // validate your UUID as you like
        }

        // extend your UUID as you like
    }

You will need a custom parser to detect your extended UUIDs.

ULID
====

``Arokettu\Uuid\Ulid``.

ULID is a different type of identifiers as described in the `ULID spec`_,
but since it has similarities to UUID like 128-bit length, was designed to solve basically same problem, and shares its
timestamp structure with UUIDv7, it was included in the library as "a very custom UUID".

Since the format lacks any indication bits, the class can be initialized directly with any 16 bytes of data.

The class implements ``TimeBasedUuid`` interface with millisecond precision.

ULID can be converted into UUIDv7 but there are caveats.
The ``isUuidV7Compatible()`` method can be used to check if the ULID is binary compatible with UUIDv7.
The factory in this library provides a UUIDv7-compatible ULID generator.
The ``toUuidV7()`` call can be used to convert compatible ULIDs.
The ``toUuidV7(lossy: true)`` call can be used to convert any ULID by forcing variant and version bits.
You can do it at your own risk if you used ULIDs and then decided to move to a more standard and supported approach.

::

    <?php

    use Arokettu\Uuid\UuidParser;

    // ULID that was converted from UUIDv7
    $ulid = UuidParser::fromBase32('01H44Q8TJ8EP0B9GNZKB6YF4J0');
    var_dump($ulid->isUuidV7Compatible());  // true
    $uuid = $ulid->toUuidV7();
    var_dump($uuid->toString());    // 01890974-6a48-7580-b4c2-bf9acde79240
    var_dump($uuid->toBase32());    // 01H44Q8TJ8EP0B9GNZKB6YF4J0

    // Just a random ULID
    $ulid = UuidParser::fromBase32('01H44RDYXJPFCF895N3BBXCZRC');
    var_dump($ulid->isUuidV7Compatible()); // false
    // $uuid = $ulid->toUuidV7(); // UnexpectedValueException: This ULID cannot be converted to UUID v7 losslessly
    $uuid = $ulid->toUuidV7(lossy: true);
    // note digit 13 becoming '7' and digit 17 moving into [89ab] range
    var_dump($uuid->toString());    // 01890986-fbb2-73d8-b424-b51ad7d67f0c
    var_dump($ulid->toRfc4122());   // 01890986-fbb2-b3d8-f424-b51ad7d67f0c
    var_dump($uuid->toBase32());    // 01H44RDYXJEFCB895N3BBXCZRC
    var_dump($ulid->toString());    // 01H44RDYXJPFCF895N3BBXCZRC

.. _RFC 4122: https://datatracker.ietf.org/doc/html/rfc4122
.. _RFC 4122 draft: https://datatracker.ietf.org/doc/html/draft-peabody-dispatch-new-uuid-format
.. _ULID spec: https://github.com/ulid/spec