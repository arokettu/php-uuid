Factory
#######

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
I may add support for them later if there will be demand or I will have time.

Version 3
---------

Version 4
---------

``Arokettu\Uuid\UuidFactory::v4()``

No input data, just randomness.
You can override RNG by passing an instance of ``Random\Randomizer``.

Version 5
---------

Version 7
---------

Version 8
---------

RFC 4122 Namespaces
===================

ULID
====

Monotonic Sequences
===================
