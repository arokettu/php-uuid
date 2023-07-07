Factories
#########

.. highlight:: php

Special UUIDs
=============

Nil UUID
--------

``Arokettu\Uuid\UuidFactory::nil()``

Max UUID
--------

``Arokettu\Uuid\UuidFactory::max()``

RFC 4122
========

This factory can create UUID versions 3, 4, 5, 7, 8.
Versions 1, 2, 6 can be considered legacy and should not be used in any non-legacy purposes.
I may add support for them later if there is demand or I have time.

Version 3
---------

``Arokettu\Uuid\UuidFactory::v3($namespace, $identifier)``

Version 3 is created from an UUID namespace and a string identifier.

::

    <?php

    use Arokettu\Uuid\UuidFactory;
    use Arokettu\Uuid\UuidNamespaces;
    use Arokettu\Uuid\UuidParser;

    $uuid = UuidFactory::v3(
        UuidParser::fromString('3113466c-5574-4391-bc27-1fd747c6be7c'),
        'some_id'
    );
    var_dump($uuid->toString()); // 09e0a238-92c9-32b2-93c1-d805976f6890

    // use a predefined namespace
    $uuid = UuidFactory::v3(UuidNamespaces::url(), 'http://example.com');
    var_dump($uuid->toString()); // d632b50c-7913-3137-ae9a-2d93f56e70d5

Version 4
---------

``Arokettu\Uuid\UuidFactory::v4()``

No input data, just randomness.
You can override RNG by passing an instance of ``Random\Randomizer``::

    <?php

    require __DIR__ . '/../vendor/autoload.php';

    use Arokettu\Uuid\UuidFactory;
    use Random\Engine\Xoshiro256StarStar;
    use Random\Randomizer;

    $uuid = UuidFactory::v4();
    var_dump($uuid->toString()); // some random uuid

    // predictable UUID for testing
    $uuid = UuidFactory::v4(randomizer: new Randomizer(new Xoshiro256StarStar(123)));
    var_dump($uuid->toString()); // f969a0d1-a18f-4a32-9e4d-6d65c7e335f8

Version 5
---------

Version 5 is created from an UUID namespace and a string identifier.

``Arokettu\Uuid\UuidFactory::v5($namespace, $identifier)``

::

    <?php

    use Arokettu\Uuid\UuidFactory;
    use Arokettu\Uuid\UuidNamespaces;
    use Arokettu\Uuid\UuidParser;

    $uuid = UuidFactory::v5(
        UuidParser::fromString('3113466c-5574-4391-bc27-1fd747c6be7c'),
        'some_id'
    );
    var_dump($uuid->toString()); // 741b80e9-31e6-51fb-8c95-07f2d392e98f

    // use a predefined namespace
    $uuid = UuidFactory::v5(UuidNamespaces::url(), 'http://example.com');
    var_dump($uuid->toString()); // 8c9ddcb0-8084-5a7f-a988-1095ab18b5df

Version 7
---------

``Arokettu\Uuid\UuidFactory::v7()``

You can set a timestamp by using an instance of ``Psr\Clock\ClockInterface``,
also you can override RNG by passing an instance of ``Random\Randomizer``::

    <?php

    use Arokettu\Clock\StaticClock;
    use Arokettu\Uuid\UuidFactory;
    use Random\Engine\Xoshiro256StarStar;
    use Random\Randomizer;

    $uuid = UuidFactory::v7();
    var_dump($uuid->toString()); // some random uuid

    // predictable UUID for testing
    // using a StaticClock isntance from the arokettu/clock package
    $uuid = UuidFactory::v7(
        clock: new StaticClock(new DateTime('2023-07-07 12:00 UTC')),
        randomizer: new Randomizer(new Xoshiro256StarStar(123)),
    );
    var_dump($uuid->toString()); // 01893039-2a00-7969-9e4d-6d65c7e335f8

Version 8
---------

``Arokettu\Uuid\UuidFactory::v8($bytes)``

Version 8 is reserved for custom implementations.
The factory accepts any sequence of 16 bytes, overwriting only variant and version bits::

    <?php

    use Arokettu\Uuid\UuidFactory;

    $uuid = UuidFactory::v8('any 16bytes here');
    var_dump($uuid->toString()); // 616e7920-3136-8279-b465-732068657265

    // example: experimental namespace UUID based on sha3
    $hash = hash_hmac('sha3-224', 'test', 'namespace', binary: true);
    $uuid = UuidFactory::v8(substr($hash, 0, 16));
    var_dump($uuid->toString()); // ab2a3a38-30a3-8def-89cd-72e79f1a5423

RFC 4122 Namespaces
===================

``Arokettu\Uuid\UuidNamespaces``

Predefined namespaces:

* ``UuidNamespaces::dns()``: ``{6ba7b810-9dad-11d1-80b4-00c04fd430c8}``
* ``UuidNamespaces::url()``: ``{6ba7b811-9dad-11d1-80b4-00c04fd430c8}``
* ``UuidNamespaces::oid()``: ``{6ba7b812-9dad-11d1-80b4-00c04fd430c8}``
* ``UuidNamespaces::x500()``: ``{6ba7b814-9dad-11d1-80b4-00c04fd430c8}``

ULID
====

``Arokettu\Uuid\UlidFactory::ulid()``

You can set a timestamp by using an instance of ``Psr\Clock\ClockInterface``,
also you can override RNG by passing an instance of ``Random\Randomizer``::

    <?php

    require __DIR__ . '/../vendor/autoload.php';

    use Arokettu\Clock\StaticClock;
    use Arokettu\Uuid\UlidFactory;
    use Random\Engine\Xoshiro256StarStar;
    use Random\Randomizer;

    $uuid = UlidFactory::ulid();
    var_dump($uuid->toString()); // some random ulid

    // predictable ULID for testing
    // using a StaticClock isntance from the arokettu/clock package
    $ulid = UlidFactory::ulid(
        clock: new StaticClock(new DateTime('2023-07-07 12:00 UTC')),
        randomizer: new Randomizer(new Xoshiro256StarStar(123)),
    );
    var_dump($ulid->toString()); // 01H4R3JAG0Z5MT1MD1HXD34QJD

Monotonic Sequences
===================
