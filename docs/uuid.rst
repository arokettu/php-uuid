UUID Versions
#############

.. highlight:: php

.. danger::
    If you use UUIDs as security tokens, well, first, please reconsider,
    but if you absolutely have to, use UUIDv4 with the Secure random engine::

        <?php

        use Arokettu\Uuid\SequenceFactory;
        use Arokettu\Uuid\UuidFactory;
        use Random\Engine\Secure;
        use Random\Randomizer;

        $secureUuid = UuidFactory::v4(new Randomizer(new Secure()));
        // or
        $secureUuidSeq = SequenceFactory::v4(new Randomizer(new Secure()));

    DO NOT use other versions and DO NOT use the default RNG.

    UUIDv4 is slightly below the recommended secure randomness (122 bits out of 128 recommended currently),
    other versions have even less.
    UUIDv1, v2, v6 have up to 62 (54 for v2) if you use both random nodes and random clock sequences.
    UUIDv7 has 74 bits maximum.
    Same goes for ULID that has 80.
    UUIDv3 and v5 are not random at all.
    With UUIDv8 you can only achieve the same security level as UUIDv4 because UUID metadata will take 6 bits anyway.

    The default RNG selection was done with performance and reasonable collision avoidance in mind,
    not with cryptography-level security.

Special
=======

Nil UUID
--------

``Arokettu\Uuid\NilUuid``

``{00000000-0000-0000-0000-000000000000}``

Nil UUID as described in the `RFC 9562`_.
The class can be constructed directly, its constructor does not accept any parameters.

Max UUID
--------

``Arokettu\Uuid\MaxUuid``

``{ffffffff-ffff-ffff-ffff-ffffffffffff}``

Max UUID as described in the `RFC 9562`_.
The class can be constructed directly, its constructor does not accept any parameters.

Generic UUID
------------

``Arokettu\Uuid\GenericUuid``

Any UUID that is not Nil, Max or Variant10xx and is not marked as ULID will be parsed to this class.

The class can also be initialized directly with any 32 hexadecimal digits.
(Obviously, the class will not be cast to a version class in case the data happen to be a valid version or special UUID)

RFC 9562
========

Variant 10xx versions described in `RFC 9562`_.

Any class can be initialized directly by 32 hexadecimal digits but the correct variant and version bits must be set.

Version 1
---------

``Arokettu\Uuid\UuidV1``.

The class implements ``TimeBasedUuid`` interface.
UUIDv1 timestamp is measured to 100 nsec precision which is more than DateTime can handle
therefore the returned value will be truncated by one decimal.
The range is ``1582-10-15 00:00:00.0 +00:00`` -- ``5236-03-31 21:21:00.684697 +00:00``.

UUIDv1 can be rearranged without loss of any data and precision into a lexicographically monotonic UUIDv6.
Use the ``toUuidV6()`` method for that::

    <?php

    use Arokettu\Uuid\UuidParser;

    $uuid1 = UuidParser::fromString('6ba7b811-9dad-11d1-80b4-00c04fd430c8');
    $uuid6 = $uuid1->toUuidV6();
    var_dump($uuid6->toString()); // 1d19dad6-ba7b-6811-80b4-00c04fd430c8

Version 2
---------

.. versionadded:: 2.3 ``getDomain()`` and ``getIdentifier()``

``Arokettu\Uuid\UuidV2``.

"DCE security" legacy UUID.
Preferably they should never be used.

The class implements ``TimeBasedUuid`` interface with 429'496'729'600 nsec precision (approximately 7 minutes)
due to truncated timestamp field compared to V1.
The range is ``1582-10-15 00:00:00.0 +00:00`` -- ``5236-03-31 21:13:51.187968 +00:00``.

The library allows you to retrieve domain and identifier values::

    <?php

    use Arokettu\Uuid\UuidParser;

    $uuid = UuidParser::fromString('000004d2-92e8-21ed-8100-3fdb0085247e');

    var_dump($uuid->getDomain()); // 0
    var_dump($uuid->getIdentifier()); // 1234

.. warning:: Identifier is an unsigned 32-bit value, it is possible to get an overflow error on a 32-bit system.

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
They are mostly useful as a conversion from UUIDv1.

The class implements ``TimeBasedUuid`` interface.
UUIDv6 timestamp is measured to 100 nsec precision which is more than DateTime can handle
therefore the returned value will be truncated by one decimal.
The range is ``1582-10-15 00:00:00.0 +00:00`` -- ``5236-03-31 21:21:00.684697 +00:00``.

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

The class implements ``TimeBasedUuid`` interface with millisecond precision
in range ``1970-01-01 00:00:00 +00:00`` -- ``10889-08-02 05:31:50.655 +00:00``.

UUIDv7 without any bit change can be converted to a ULID.
Use ``toUlid()`` for that::

    <?php

    use Arokettu\Uuid\UuidParser;

    $uuid = UuidParser::fromString('01890974-6a48-7580-b4c2-bf9acde79240');
    $ulid = $uuid->toUlid();
    var_dump($ulid->toString());    // 01H44Q8TJ8EP0B9GNZKB6YF4J0
    var_dump($ulid->toRfcFormat());   // 01890974-6a48-7580-b4c2-bf9acde79240

Version 8
---------

``Arokettu\Uuid\UuidV8``.

This is a special version for custom UUIDs.
The class can be extended::

    <?php

    readonly class UuidExtended extends UuidV8
    {
        protected function customAssertValid(string $hex): void
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

Since the format lacks any indication bits, the class can be initialized directly with any 32 hexadecimal digits.

The class implements ``TimeBasedUuid`` interface with millisecond precision
in range ``1970-01-01 00:00:00 +00:00`` -- ``10889-08-02 05:31:50.655 +00:00``.

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
    $ulid = UlidParser::fromBase32('01H44Q8TJ8EP0B9GNZKB6YF4J0');
    var_dump($ulid->isUuidV7Compatible());  // true
    $uuid = $ulid->toUuidV7();
    var_dump($uuid->toString());    // 01890974-6a48-7580-b4c2-bf9acde79240
    var_dump($uuid->toBase32());    // 01H44Q8TJ8EP0B9GNZKB6YF4J0

    // Just a random ULID
    $ulid = UlidParser::fromBase32('01H44RDYXJPFCF895N3BBXCZRC');
    var_dump($ulid->isUuidV7Compatible()); // false
    // $uuid = $ulid->toUuidV7(); // UnexpectedValueException: This ULID cannot be converted to UUID v7 losslessly
    $uuid = $ulid->toUuidV7(lossy: true);
    // note digit 13 becoming '7' and digit 17 moving into [89ab] range
    var_dump($uuid->toString());    // 01890986-fbb2-73d8-b424-b51ad7d67f0c
    var_dump($ulid->toRfcFormat()); // 01890986-fbb2-b3d8-f424-b51ad7d67f0c
    var_dump($uuid->toBase32());    // 01H44RDYXJEFCB895N3BBXCZRC
    var_dump($ulid->toString());    // 01H44RDYXJPFCF895N3BBXCZRC

.. _RFC 9562: https://datatracker.ietf.org/doc/html/rfc9562
.. _ULID spec: https://github.com/ulid/spec
