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

This factory can create UUID versions 1, 3, 4, 5, 6, 7, 8.
Version 2 can be considered legacy and should not be used in any non-legacy purposes.
Versions 1 and 6 are not recommended either.

Version 1
---------

``Arokettu\Uuid\UuidFactory::v1($node)``

Set :ref:`a node <uuidv1nodes>` if needed, a random one will be used if not set.
You can set a timestamp by using an instance of ``Psr\Clock\ClockInterface``,
also you can override RNG by passing an instance of ``Random\Randomizer``::

    <?php

    use Arokettu\Clock\StaticClock;
    use Arokettu\Uuid\Node\StaticNode;
    use Arokettu\Uuid\UuidFactory;
    use Random\Engine\Xoshiro256StarStar;
    use Random\Randomizer;

    $uuid = UuidFactory::v1(); // some random UUID

    $node = StaticNode::fromHex('1234567890ab');
    $rand = new Randomizer(new Xoshiro256StarStar(123));
    $clock = new StaticClock(new DateTime('2023-10-30 12:00 UTC'));
    $uuid = UuidFactory::v1($node, $clock, $rand);

    var_dump($uuid->toString()); // d9fb2000-771b-11ee-b969-1334567890ab

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

Version 6
---------

``Arokettu\Uuid\UuidFactory::v6($node)``

Set :ref:`a node <uuidv1nodes>` if needed, a random one will be used if not set.
You can set a timestamp by using an instance of ``Psr\Clock\ClockInterface``,
also you can override RNG by passing an instance of ``Random\Randomizer``::

    <?php

    use Arokettu\Clock\StaticClock;
    use Arokettu\Uuid\Node\StaticNode;
    use Arokettu\Uuid\UuidFactory;
    use Random\Engine\Xoshiro256StarStar;
    use Random\Randomizer;

    $uuid = UuidFactory::v6(); // some random UUID

    $node = StaticNode::fromHex('1234567890ab');
    $rand = new Randomizer(new Xoshiro256StarStar(123));
    $clock = new StaticClock(new DateTime('2023-10-30 12:00 UTC'));
    $uuid = UuidFactory::v6($node, $clock, $rand);

    var_dump($uuid->toString()); // 1ee771bd-9fb2-6000-b969-1334567890ab

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

Sequences
=========

Sequences are designed to be used in a case where you need a lot of UUIDs in a single process.
Sequences for UUIDv1, v6, v7, and ULID also enforce extra monotonicity
for IDs created in the same millisecond/microsecond.
There are no sequences for UUIDv3 and UUIDv5 because they are not sequential by nature.
The sequences are designed to provide a continuous supply of IDs, advancing the timestamp when clock sequences overflow.
All sequences implement ``Traversable``.

::

    <?php

    use Arokettu\Uuid\SequenceFactory;

    $seq = SequenceFactory::v7();

    foreach ($seq as $uuid) {
        echo $uuid, PHP_EOL; // infinite supply of monotonic UUIDs
    }

UUIDv1
------

``Arokettu\Uuid\SequenceFactory::v1($node)``

This sequence uses 14 bit of clock_seq and the lowest decimal of the timestamp as a clock sequence.
The sequence is initialized with a randomly generated static node ID if another node ID generator is not supplied.

Like with the regular factory you can set a timestamp by using an instance of ``Psr\Clock\ClockInterface``
and override RNG by passing an instance of ``Random\Randomizer``.

::

    <?php

    use Arokettu\Clock\StaticClock;
    use Arokettu\Uuid\SequenceFactory;
    use Random\Engine\Xoshiro256StarStar;
    use Random\Randomizer;

    $seq = SequenceFactory::v1(
        clock: new StaticClock(new DateTime('2023-07-07 12:00 UTC')),
        randomizer: new Randomizer(new Xoshiro256StarStar(123)),
    );

    for ($i = 0; $i < 10; $i++) {
        echo $seq->next(), PHP_EOL;
    }

    // cc79e000-1cbd-11ee-8d5e-f969a0d1a18f
    // cc79e000-1cbd-11ee-8d5f-f969a0d1a18f
    // cc79e000-1cbd-11ee-8d60-f969a0d1a18f
    // cc79e000-1cbd-11ee-8d61-f969a0d1a18f
    // cc79e000-1cbd-11ee-8d62-f969a0d1a18f
    // cc79e000-1cbd-11ee-8d63-f969a0d1a18f
    // cc79e000-1cbd-11ee-8d64-f969a0d1a18f
    // cc79e000-1cbd-11ee-8d65-f969a0d1a18f
    // cc79e000-1cbd-11ee-8d66-f969a0d1a18f
    // cc79e000-1cbd-11ee-8d67-f969a0d1a18f

UUIDv4
------

``Arokettu\Uuid\SequenceFactory::v4()``

Just a sequence of random UUIDv4.
This sequence is not monotonic and exists only for convenience.

Like with the regular factory you can override RNG by passing an instance of ``Random\Randomizer``.

::

    <?php

    use Arokettu\Uuid\SequenceFactory;
    use Random\Engine\Xoshiro256StarStar;
    use Random\Randomizer;

    $seq = SequenceFactory::v4(
        randomizer: new Randomizer(new Xoshiro256StarStar(123)),
    );

    for ($i = 0; $i < 10; $i++) {
        echo $seq->next(), PHP_EOL;
    }

    // f969a0d1-a18f-4a32-9e4d-6d65c7e335f8
    // 2fa6f2c3-462b-4a77-8682-cfaa99028220
    // de789d95-b3d8-4856-aa28-295af8ebf9ff
    // 1b75f844-9b23-4260-951a-7e9d570a1aa8
    // d4df5c6d-af02-43c2-b05c-234f8095766f
    // ba374ea8-3797-47a6-8d48-f3844e4600c4
    // c52aff91-89fc-4e09-b434-29e798cd8c51
    // 704cae21-5dcb-4ca9-93b3-3da29b3d812f
    // 3405283f-75a9-4a52-a645-4ba0df565fbc
    // efebcd8e-c7ea-4486-8f66-63a8e581821f

UUIDv6
------

``Arokettu\Uuid\SequenceFactory::v6($node)``

This sequence uses 14 bit of clock_seq and the lowest decimal of the timestamp as a clock sequence.
The sequence is initialized with a randomly generated static node ID if another node ID generator is not supplied.

Like with the regular factory you can set a timestamp by using an instance of ``Psr\Clock\ClockInterface``
and override RNG by passing an instance of ``Random\Randomizer``.

::

    <?php

    use Arokettu\Clock\StaticClock;
    use Arokettu\Uuid\SequenceFactory;
    use Random\Engine\Xoshiro256StarStar;
    use Random\Randomizer;

    $seq = SequenceFactory::v6(
        clock: new StaticClock(new DateTime('2023-07-07 12:00 UTC')),
        randomizer: new Randomizer(new Xoshiro256StarStar(123)),
    );

    for ($i = 0; $i < 10; $i++) {
        echo $seq->next(), PHP_EOL;
    }

    // 1ee1cbdc-c79e-6000-8d5e-f969a0d1a18f
    // 1ee1cbdc-c79e-6000-8d5f-f969a0d1a18f
    // 1ee1cbdc-c79e-6000-8d60-f969a0d1a18f
    // 1ee1cbdc-c79e-6000-8d61-f969a0d1a18f
    // 1ee1cbdc-c79e-6000-8d62-f969a0d1a18f
    // 1ee1cbdc-c79e-6000-8d63-f969a0d1a18f
    // 1ee1cbdc-c79e-6000-8d64-f969a0d1a18f
    // 1ee1cbdc-c79e-6000-8d65-f969a0d1a18f
    // 1ee1cbdc-c79e-6000-8d66-f969a0d1a18f
    // 1ee1cbdc-c79e-6000-8d67-f969a0d1a18f

UUIDv7
------

``Arokettu\Uuid\SequenceFactory::v7()``

The chosen algorithm is 12 bit clock sequence in rand_a + random 'tail' in rand_b
as described in `RFC 4122`_ (Draft 4) 6.2 Method 1.
It gives a guaranteed sequence of 2049 UUIDs per millisecond (actual number is random, up to 4096).

Like with the regular factory you can set a timestamp by using an instance of ``Psr\Clock\ClockInterface``
and override RNG by passing an instance of ``Random\Randomizer``.

::

    <?php

    use Arokettu\Clock\StaticClock;
    use Arokettu\Uuid\SequenceFactory;
    use Random\Engine\Xoshiro256StarStar;
    use Random\Randomizer;

    $seq = SequenceFactory::v7(
        clock: new StaticClock(new DateTime('2023-07-07 12:00 UTC')),
        randomizer: new Randomizer(new Xoshiro256StarStar(123)),
    );

    for ($i = 0; $i < 10; $i++) {
        echo $seq->next(), PHP_EOL;
    }

    // 01893039-2a00-71f9-9e4d-6d65c7e335f8
    // 01893039-2a00-71fa-afa6-f2c3462baa77
    // 01893039-2a00-71fb-8682-cfaa99028220
    // 01893039-2a00-71fc-9e78-9d95b3d87856
    // 01893039-2a00-71fd-aa28-295af8ebf9ff
    // 01893039-2a00-71fe-9b75-f8449b23c260
    // 01893039-2a00-71ff-951a-7e9d570a1aa8
    // 01893039-2a00-7200-94df-5c6daf02d3c2
    // 01893039-2a00-7201-b05c-234f8095766f
    // 01893039-2a00-7202-ba37-4ea83797f7a6

.. note:: See ULID section for a way to generate a longer sequence than 2049.

ULID
----

``Arokettu\Uuid\SequenceFactory::ulid($uuidV7Compatible = false)``

The algorithm is a simplified version of ULID standard algo, having the whole rand_a + rand_b as a counter,
that also aligns with `RFC 4122`_ (Draft 4) 6.2 Method 2.
The simplification is that only the lowest 3 bytes act as a proper counter to simplify the 32 bit implementation.
It gives a sequence up to 16'777'216 ULIDs per millisecond (actual number is random).

Like with the regular factory you can set a timestamp by using an instance of ``Psr\Clock\ClockInterface``
and override RNG by passing an instance of ``Random\Randomizer``.

::

    <?php

    use Arokettu\Clock\StaticClock;
    use Arokettu\Uuid\SequenceFactory;
    use Random\Engine\Xoshiro256StarStar;
    use Random\Randomizer;

    $seq = SequenceFactory::ulid(
        clock: new StaticClock(new DateTime('2023-07-07 12:00 UTC')),
        randomizer: new Randomizer(new Xoshiro256StarStar(123)),
    );

    for ($i = 0; $i < 10; $i++) {
        echo $seq->next(), PHP_EOL;
    }

    // 01H4R3JAG0Z5MT1MD1HXD6TKAY
    // 01H4R3JAG0Z5MT1MD1HXD6TKAZ
    // 01H4R3JAG0Z5MT1MD1HXD6TKB0
    // 01H4R3JAG0Z5MT1MD1HXD6TKB1
    // 01H4R3JAG0Z5MT1MD1HXD6TKB2
    // 01H4R3JAG0Z5MT1MD1HXD6TKB3
    // 01H4R3JAG0Z5MT1MD1HXD6TKB4
    // 01H4R3JAG0Z5MT1MD1HXD6TKB5
    // 01H4R3JAG0Z5MT1MD1HXD6TKB6
    // 01H4R3JAG0Z5MT1MD1HXD6TKB7

``$uuidV7Compatible`` param allows you to create ULIDs that are bit-compatible with UUIDv7 by setting proper version and variant bits.
Among other uses (like the ability to switch to UUIDs in future) it allows you to create UUIDv7 sequences longer than 2049 (but less random and more predictable)::

    <?php

    use Arokettu\Clock\StaticClock;
    use Arokettu\Uuid\SequenceFactory;
    use Random\Engine\Xoshiro256StarStar;
    use Random\Randomizer;

    $seq = SequenceFactory::ulid(
        true, // build with proper bits
        clock: new StaticClock(new DateTime('2023-07-07 12:00 UTC')),
        randomizer: new Randomizer(new Xoshiro256StarStar(123)),
    );

    for ($i = 0; $i < 10; $i++) {
        echo $seq->next()->toUuidV7(), PHP_EOL;
    }

    // 01893039-2a00-7969-a0d1-a18f5a6d4d5e
    // 01893039-2a00-7969-a0d1-a18f5a6d4d5f
    // 01893039-2a00-7969-a0d1-a18f5a6d4d60
    // 01893039-2a00-7969-a0d1-a18f5a6d4d61
    // 01893039-2a00-7969-a0d1-a18f5a6d4d62
    // 01893039-2a00-7969-a0d1-a18f5a6d4d63
    // 01893039-2a00-7969-a0d1-a18f5a6d4d64
    // 01893039-2a00-7969-a0d1-a18f5a6d4d65
    // 01893039-2a00-7969-a0d1-a18f5a6d4d66
    // 01893039-2a00-7969-a0d1-a18f5a6d4d67

.. _RFC 4122: https://datatracker.ietf.org/doc/html/draft-peabody-dispatch-new-uuid-format
