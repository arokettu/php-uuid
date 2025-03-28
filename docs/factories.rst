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

Variant 10xx
============

This factory can create UUID versions 1-8.
Version 2 can be considered legacy and should not be used in any non-legacy purposes.
Versions 1 and 6 are not recommended either.

Version 1
---------

.. versionchanged:: 3.1 Passing ``DateTime`` / ``DateTimeImmutable`` is allowed
.. versionchanged:: 4.0
   ``$clock`` is renamed to ``$timestamp``.
   Passing ``int`` / ``float`` timestamps is allowed
.. versionadded:: 4.0 ``$clockSequence``

.. note::
    ``float`` timestamps are internally converted to DateTime so precision below 1 microsecond is still not achievable.

``Arokettu\Uuid\UuidFactory::v1($node)``

Set :ref:`a node <uuidv1nodes>` if needed, a random one will be used if not set.
You can set a timestamp by using an instance of ``DateTimeInterface`` or ``Psr\Clock\ClockInterface``,
a clock sequence value by using an integer in range 0-16'383,
also you can override RNG by passing an instance of ``Random\Randomizer``::

    <?php

    use Arokettu\Uuid\Nodes\StaticNode;
    use Arokettu\Uuid\UuidFactory;
    use Random\Engine\Xoshiro256StarStar;
    use Random\Randomizer;

    $uuid = UuidFactory::v1(); // some random UUID

    // nothing random
    $node = StaticNode::fromHex('1234567890ab');
    $time = new DateTime('2023-10-30 12:00 UTC');
    $seq = 123;
    $uuid = UuidFactory::v1($node, $seq, $time);

    var_dump($uuid->toString()); // d9fb2000-771b-11ee-807b-1334567890ab

    // use RNG to get predictable random clock sequence and node values
    $rng = new Randomizer(new Xoshiro256StarStar(123));
    $time = new DateTime('2023-10-30 12:00 UTC');
    $uuid = UuidFactory::v1(timestamp: $time, randomizer: $rng);

    var_dump($uuid->toString()); // d9fb2000-771b-11ee-a9f9-5f4d6d65c7e3

Version 2
---------

.. versionadded:: 2.3
.. versionchanged:: 3.1 Passing ``DateTime`` / ``DateTimeImmutable`` is allowed
.. versionchanged:: 4.0
   ``$clock`` is renamed to ``$timestamp``.
   Passing ``int`` / ``float`` timestamps is allowed
.. versionadded:: 4.0 ``$clockSequence``

``Arokettu\Uuid\UuidFactory::v2($domain, $identifier, $node)``

.. note::
    This is a legacy version and it should not be used.
    There is a high chance of generating a same ID unless a random node is used.

Version 2 requires domain (8 bit unsigned) and identifier (32 bit unsigned) values.

Set :ref:`a node <uuidv1nodes>` if needed, a random one will be used if not set.
You can set a timestamp by using an instance of ``DateTimeInterface`` or ``Psr\Clock\ClockInterface``,
a clock sequence value by using an integer in range 0-63,
also you can override RNG by passing an instance of ``Random\Randomizer``::

    <?php

    use Arokettu\Uuid\DceSecurity\Domains;
    use Arokettu\Uuid\Nodes\StaticNode;
    use Arokettu\Uuid\UuidFactory;
    use Random\Engine\Xoshiro256StarStar;
    use Random\Randomizer;

    $uuid = UuidFactory::v2(Domains::GROUP, posix_getgid()); // some GID based UUID

    // nothing random
    $node = StaticNode::fromHex('1234567890ab');
    $time = new DateTime('2023-10-30 12:00 UTC');
    $seq = 23;
    $domain = Domains::PERSON;
    $identifier = posix_getuid(); // usually 1000 for most default groups on modern Linuxes
    $uuid = UuidFactory::v2($domain, $identifier, $node, $seq, $time);

    var_dump($uuid->toString()); // 000003e8-771b-21ee-9700-1334567890ab

    // use RNG to get predictable random clock sequence and node values
    $rng = new Randomizer(new Xoshiro256StarStar(123));
    // reusing $time, $domain, $identifier
    $uuid = UuidFactory::v2($domain, $identifier, timestamp: $time, randomizer: $rng);

    var_dump($uuid->toString()); // 000003e8-771b-21ee-b900-5f4d6d65c7e3

Version 3
---------

``Arokettu\Uuid\UuidFactory::v3($namespace, $identifier)``

Version 3 is created from an UUID namespace and a string identifier::

    <?php

    use Arokettu\Uuid\Namespaces\UuidNamespace;
    use Arokettu\Uuid\UuidFactory;
    use Arokettu\Uuid\UuidParser;

    $uuid = UuidFactory::v3(
        UuidParser::fromString('3113466c-5574-4391-bc27-1fd747c6be7c'),
        'some_id'
    );
    var_dump($uuid->toString()); // 09e0a238-92c9-32b2-93c1-d805976f6890

    // use a predefined namespace
    $uuid = UuidFactory::v3(UuidNamespace::URL, 'http://example.com');
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

    use Arokettu\Uuid\Namespaces\UuidNamespace;
    use Arokettu\Uuid\UuidFactory;
    use Arokettu\Uuid\UuidParser;

    $uuid = UuidFactory::v5(
        UuidParser::fromString('3113466c-5574-4391-bc27-1fd747c6be7c'),
        'some_id'
    );
    var_dump($uuid->toString()); // 741b80e9-31e6-51fb-8c95-07f2d392e98f

    // use a predefined namespace
    $uuid = UuidFactory::v5(UuidNamespace::URL, 'http://example.com');
    var_dump($uuid->toString()); // 8c9ddcb0-8084-5a7f-a988-1095ab18b5df

Version 6
---------

.. versionchanged:: 3.1 Passing ``DateTime`` / ``DateTimeImmutable`` is allowed
.. versionchanged:: 4.0
   ``$clock`` is renamed to ``$timestamp``.
   Passing ``int`` / ``float`` timestamps is allowed
.. versionadded:: 4.0 ``$clockSequence``

.. note::
    ``float`` timestamps are internally converted to DateTime so precision below 1 microsecond is still not achievable.

``Arokettu\Uuid\UuidFactory::v6($node)``

Set :ref:`a node <uuidv1nodes>` if needed, a random one will be used if not set.
You can set a timestamp by using an instance of ``DateTimeInterface`` or ``Psr\Clock\ClockInterface``,
a clock sequence value by using an integer in range 0-16'383,
also you can override RNG by passing an instance of ``Random\Randomizer``::

    <?php

    use Arokettu\Uuid\Nodes\StaticNode;
    use Arokettu\Uuid\UuidFactory;
    use Random\Engine\Xoshiro256StarStar;
    use Random\Randomizer;

    $uuid = UuidFactory::v6(); // some random UUID

    // nothing random
    $node = StaticNode::fromHex('1234567890ab');
    $time = new DateTime('2023-10-30 12:00 UTC');
    $seq = 123;
    $uuid = UuidFactory::v6($node, $seq, $time);

    var_dump($uuid->toString()); // 1ee771bd-9fb2-6000-807b-1334567890ab

    // use RNG to get predictable random clock sequence and node values
    $rng = new Randomizer(new Xoshiro256StarStar(123));
    $time = new DateTime('2023-10-30 12:00 UTC');
    $uuid = UuidFactory::v6(timestamp: $time, randomizer: $rng);

    var_dump($uuid->toString()); // 1ee771bd-9fb2-6000-a9f9-5f4d6d65c7e3

Version 7
---------

.. versionchanged:: 3.1 Passing ``DateTime`` / ``DateTimeImmutable`` is allowed
.. versionchanged:: 4.0
   ``$clock`` is renamed to ``$timestamp``.
   Passing ``int`` / ``float`` timestamps is allowed

``Arokettu\Uuid\UuidFactory::v7()``

You can set a timestamp by using an instance of ``DateTimeInterface`` or ``Psr\Clock\ClockInterface``,
also you can override RNG by passing an instance of ``Random\Randomizer``::

    <?php

    use Arokettu\Clock\StaticClock;
    use Arokettu\Uuid\UuidFactory;
    use Random\Engine\Xoshiro256StarStar;
    use Random\Randomizer;

    $uuid = UuidFactory::v7();
    var_dump($uuid->toString()); // some random uuid

    // predictable UUID for testing
    $uuid = UuidFactory::v7(
        timestamp: new DateTime('2023-07-07 12:00 UTC'),
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

ULID
====

.. versionchanged:: 3.1 Passing ``DateTime`` / ``DateTimeImmutable`` is allowed
.. versionchanged:: 4.0
   ``$clock`` is renamed to ``$timestamp``.
   Passing ``int`` / ``float`` timestamps is allowed

``Arokettu\Uuid\UlidFactory::ulid()``

You can set a timestamp by using an instance of ``DateTimeInterface`` or ``Psr\Clock\ClockInterface``,
also you can override RNG by passing an instance of ``Random\Randomizer``::

    <?php

    use Arokettu\Clock\StaticClock;
    use Arokettu\Uuid\UlidFactory;
    use Random\Engine\Xoshiro256StarStar;
    use Random\Randomizer;

    $uuid = UlidFactory::ulid();
    var_dump($uuid->toString()); // some random ulid

    // predictable ULID for testing
    $ulid = UlidFactory::ulid(
        timestamp: new DateTime('2023-07-07 12:00 UTC'),
        randomizer: new Randomizer(new Xoshiro256StarStar(123)),
    );
    var_dump($ulid->toString()); // 01H4R3JAG0Z5MT1MD1HXD34QJD

Sequences
=========

Sequences are designed to be used in a case where you need a lot of UUIDs in a single process.
Sequences for UUIDv1, v6, v7, and ULID also enforce extra monotonicity
for IDs created in the same millisecond/microsecond.
There are no sequences for UUIDv3 and UUIDv5 because they are not sequential by nature.
The sequences are designed to provide a continuous supply of IDs, advancing the timestamp when counters overflow.
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

``Arokettu\Uuid\SequenceFactory::v1($node, $clockSequence)``
``Arokettu\Uuid\SequenceFactory::v1FromPrototype(UuidV1|UuidV6 $prototype)``

This sequence uses the lowest decimal of the timestamp as a counter.
The sequence is initialized with a randomly generated static node ID and randomly generated static clock sequence.
Pass an instance of ``Arokettu\Uuid\Nodes\Node`` to override the node strategy.
Pass the integer clock sequence value to use a predefined clock sequence
or a special ``Arokettu\Uuid\ClockSequences\ClockSequence::Random`` object to generate a new clock sequence value for every UUID.

The prototype factory allows you to preset a node and a clock sequence from an existing UUID.

Like with the regular factory you can set a timestamp by using an instance of ``Psr\Clock\ClockInterface``
and override RNG by passing an instance of ``Random\Randomizer``.

.. note::
    Randomizer is only used if you have random/randomly initialized node or random/randomly initialized clock sequence.

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
    // cc79e001-1cbd-11ee-8d5e-f969a0d1a18f
    // cc79e002-1cbd-11ee-8d5e-f969a0d1a18f
    // cc79e003-1cbd-11ee-8d5e-f969a0d1a18f
    // cc79e004-1cbd-11ee-8d5e-f969a0d1a18f
    // cc79e005-1cbd-11ee-8d5e-f969a0d1a18f
    // cc79e006-1cbd-11ee-8d5e-f969a0d1a18f
    // cc79e007-1cbd-11ee-8d5e-f969a0d1a18f
    // cc79e008-1cbd-11ee-8d5e-f969a0d1a18f
    // cc79e009-1cbd-11ee-8d5e-f969a0d1a18f

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

``Arokettu\Uuid\SequenceFactory::v6($node, $clockSequence)``
``Arokettu\Uuid\SequenceFactory::v6FromPrototype(UuidV1|UuidV6 $prototype)``

This sequence uses the lowest decimal of the timestamp as a counter.
The sequence is initialized with a randomly generated static node ID and randomly generated static clock sequence.
Pass an instance of ``Arokettu\Uuid\Nodes\Node`` to override the node strategy.
Pass the integer clock sequence value to use a predefined clock sequence
or a special ``Arokettu\Uuid\ClockSequences\ClockSequence::Random`` object to generate a new clock sequence value for every UUID.

The prototype factory allows you to preset a node and a clock sequence from an existing UUID.

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
    // 1ee1cbdc-c79e-6001-8d5e-f969a0d1a18f
    // 1ee1cbdc-c79e-6002-8d5e-f969a0d1a18f
    // 1ee1cbdc-c79e-6003-8d5e-f969a0d1a18f
    // 1ee1cbdc-c79e-6004-8d5e-f969a0d1a18f
    // 1ee1cbdc-c79e-6005-8d5e-f969a0d1a18f
    // 1ee1cbdc-c79e-6006-8d5e-f969a0d1a18f
    // 1ee1cbdc-c79e-6007-8d5e-f969a0d1a18f
    // 1ee1cbdc-c79e-6008-8d5e-f969a0d1a18f
    // 1ee1cbdc-c79e-6009-8d5e-f969a0d1a18f

UUIDv7 (short)
--------------

.. versionadded:: 3.0 ``v7Short``

``Arokettu\Uuid\SequenceFactory::v7()``
``Arokettu\Uuid\SequenceFactory::v7Short()``

The chosen algorithm is 12 bit counter in rand_a + random 'tail' in rand_b as described in `RFC 9562`_ 6.2 Method 1.
It gives a guaranteed sequence of 2049 UUIDs per millisecond (actual number is random, up to 4096) that are highly unguessable.

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

UUIDv7 (long) and ULID
-----------------------

.. versionadded:: 3.0 ``v7Long``

* ``Arokettu\Uuid\SequenceFactory::v7Long()``
* ``Arokettu\Uuid\SequenceFactory::ulid($uuidV7Compatible = false)``

The algorithm is a simplified version of ULID standard algo, having the whole rand_a + rand_b as a counter,
that also aligns with `RFC 9562`_ 6.2 Method 2.
The simplification is that only the lowest 48 bits act as a proper counter to simplify the implementation.
Each iteration increments with 24 bits of randomness resulting in approximately 8'388'608 ids/msec.
This sequence is moderately unguessable.

Like with the regular factory you can set a timestamp by using an instance of ``Psr\Clock\ClockInterface``
and override RNG by passing an instance of ``Random\Randomizer``.

UUIDv7::

        <?php

        use Arokettu\Clock\StaticClock;
        use Arokettu\Uuid\SequenceFactory;
        use Random\Engine\Xoshiro256StarStar;
        use Random\Randomizer;

        $seq = SequenceFactory::v7Long(
            clock: new StaticClock(new DateTime('2023-07-07 12:00 UTC')),
            randomizer: new Randomizer(new Xoshiro256StarStar(123)),
        );

        for ($i = 0; $i < 10; $i++) {
            echo $seq->next(), PHP_EOL;
        }

        // 01893039-2a00-7969-a0d1-6d4d5ef2a62f
        // 01893039-2a00-7969-a0d1-6d4d5fc228e0
        // 01893039-2a00-7969-a0d1-6d4d605fa254
        // 01893039-2a00-7969-a0d1-6d4d6088cb19
        // 01893039-2a00-7969-a0d1-6d4d61814079
        // 01893039-2a00-7969-a0d1-6d4d61ff5b6c
        // 01893039-2a00-7969-a0d1-6d4d625c3bae
        // 01893039-2a00-7969-a0d1-6d4d627f986e
        // 01893039-2a00-7969-a0d1-6d4d62cdd0d1
        // 01893039-2a00-7969-a0d1-6d4d63c119a3

ULID::

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

    // 01H4R3JAG0Z5MT1MBD9NFF59HF
    // 01H4R3JAG0Z5MT1MBD9NFW4A70
    // 01H4R3JAG0Z5MT1MBD9NG5Z8JM
    // 01H4R3JAG0Z5MT1MBD9NG8HJRS
    // 01H4R3JAG0Z5MT1MBD9NGR2G3S
    // 01H4R3JAG0Z5MT1MBD9NGZYPVC
    // 01H4R3JAG0Z5MT1MBD9NH5REXE
    // 01H4R3JAG0Z5MT1MBD9NH7Z63E
    // 01H4R3JAG0Z5MT1MBD9NHCVM6H
    // 01H4R3JAG0Z5MT1MBD9NHW26D3

``$uuidV7Compatible`` param allows you to create ULIDs that are bit-compatible with UUIDv7 by setting proper version and variant bits::

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

    // 01893039-2a00-7969-a0d1-6d4d5ef2a62f
    // 01893039-2a00-7969-a0d1-6d4d5fc228e0
    // 01893039-2a00-7969-a0d1-6d4d605fa254
    // 01893039-2a00-7969-a0d1-6d4d6088cb19
    // 01893039-2a00-7969-a0d1-6d4d61814079
    // 01893039-2a00-7969-a0d1-6d4d61ff5b6c
    // 01893039-2a00-7969-a0d1-6d4d625c3bae
    // 01893039-2a00-7969-a0d1-6d4d627f986e
    // 01893039-2a00-7969-a0d1-6d4d62cdd0d1
    // 01893039-2a00-7969-a0d1-6d4d63c119a3

Custom UUIDs
============

``Arokettu\Uuid\NonStandard\CustomUuidFactory``

A factory for useful nonstandard UUIDs.

Sha256-based Namespace
----------------------

``Arokettu\Uuid\NonStandard\CustomUuidFactory::sha256($namespace, $identifier)``

A namespace type UUID similar to versions 3 and 5 but using sha256 as a hashing function.
The factory creates an instance of UUIDv8.
This method is shown in `RFC 9562`_ B.2 example.

::

    <?php

    use Arokettu\Uuid\Namespaces\UuidNamespace;
    use Arokettu\Uuid\NonStandard\CustomUuidFactory;

    echo CustomUuidFactory::sha256(
        UuidNamespace::DNS,
        'www.example.com'
    )->toString(); // 5c146b14-3c52-8afd-938a-375d0df1fbf6

.. _RFC 9562: https://datatracker.ietf.org/doc/html/rfc9562
