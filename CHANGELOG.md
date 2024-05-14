# Changelog

## 3.x

### 3.0.0

*May 15, 2024*

* Since RFC 9562 is published, terminology was rebased
  * `Rfc4122Variant10xxUuid` -> `Variant10xxUuid` and it no longer extends `Rfc4122Uuid`
  * `UuidV2`, `UuidV6`, `UuidV7`, `UuidV8`, `MaxUuid` no longer implement `Rfc4122Uuid`
  * `NilUuid`, `MaxUuid`, all versions except for `UuidV2` implement the new `Rfc9562Uuid` interface
  * `to/fromRfc4122` are now `to/fromRfcFormat` with `to/fromRfc4122` and `to/fromRfc9562` aliases
  * Errors referencing RFC 4122 now reference RFC 9562
* `UuidNamespaces` helper was replaced with `Namespaces\UuidNamespace` enum
* Securely seeded `PcgOneseq128XslRr64` is used instead of the secure generator
  to balance performance and security
* Exposed ULID sequence generator as UUIDv7 generator (`v7Long`)
* Added strict mode to ``fromRfcFormat()``

## 2.x

### 2.5.0

*May 2, 2024*

* Added strict mode to ``fromBase32()``

### 2.4.0

*Apr 26, 2024*

* Added `parse()` as an alias of `toString()` to `UuidParser` and `UlidParser`
* Increased ULID sequence counter to 6 bytes
  * When used with statically initialized pseudo-random number generators,
    it will produce different values, obviously

### 2.3.1

*Feb 25, 2024*

* Added bcmath calculator for 32-bit environments in addition to GMP and Unsigned

### 2.3.0

*Jan 5, 2024*

* Added UUIDv2 factory
* Added UUIDv2 `getDomain()` and `getIdentifier()` getters

### 2.2.0

*Dec 30, 2023*

* ``MacNode::system()`` no longer depends on ``getmac``
* Added ``MacNode::trySystem()``

### 2.1.1

*Dec 17, 2023*

* Fixed: when overriding RNG in the v1/v6 factory, RNG was not applied to the default random node

### 2.1.0

*Dec 17, 2023*

* Added decimal parser and decimal exporter

### 2.0.0

*Dec 7, 2023*

Forked from 1.4.5

* Removed deprecations:
  * `Rfc4122Variant1Uuid`
  * `UlidFactory::sequence()`
  * `UlidMonotonicSequence`
  * `UuidFactory::v7Sequence()`
  * `UuidV7MonotonicSequence`

## 1.x

### 1.4.5

*Dec 1, 2023*

* Fixed UlidSequence may advance counter by zero and produce the same Ulid.
  * When used with statically initialized pseudo-random number generators,
    it will produce different values, obviously

### 1.4.4

*Nov 30, 2023*

* Fixed alternative characters (i,l,o) not recognized in the first position of Base32

### 1.4.3

*Nov 23, 2023*

* Fixed UuidV6Sequence and UuidV7Sequence not implementing UuidSequence

### 1.4.2

*Nov 23, 2023*

* Speed optimizations

### 1.4.1

*Nov 15, 2023*

* Use a small date truncation lib

### 1.4.0

*Nov 11, 2023*

* UlidSequence now increases its counter with random values instead of 1 to decrease predictability
  * When used with statically initialized pseudo-random number generators,
    it will produce different values, obviously
* `NonStandard\CustomUuidFactory` for non-standard UUIDs
  * `CustomUuidFactory::sha256()`

### 1.3.0

*Nov 3, 2023*

* Factory methods to generate UUIDv1 and UUIDv6
* Speed up non-monotonic generation of UUIDv7 and ULID
* UuidSequence interface, SequenceFactory and sequences for UUIDv1, v4, v6
  * `\Arokettu\Uuid\SequenceFactory`
  * `\Arokettu\Uuid\Sequences\UuidSequence`
  * `\Arokettu\Uuid\Sequences\UuidV1Sequence`
  * `\Arokettu\Uuid\Sequences\UuidV4Sequence`
  * `\Arokettu\Uuid\Sequences\UuidV6Sequence`
  * `\Arokettu\Uuid\Sequences\UlidSequence`
* Sequence classes for UUIDv7 and ULID have been renamed, old class names and factories deprecated
  * New factories are available in `\Arokettu\Uuid\SequenceFactory`
  * `\Arokettu\Uuid\UuidV7MonotonicSequence` -> `\Arokettu\Uuid\Sequences\UuidV7Sequence`
  * `\Arokettu\Uuid\UlidMonotonicSequence` -> `\Arokettu\Uuid\Sequences\UlidSequence`
* Sequences no longer throw exception on counter overflow, they increase a timestamp by the lowest unit
  * The bit reservation parameter was removed. The highest bit is always reserved for UUIDv7 and never reserved for ULID
  * When used with statically initialized pseudo-random number generators,
    the sequences will produce different values

### 1.2.1

*Aug 4, 2023*

* Sped up factories a bit
* Fixed problems with autoload of the deprecated interface

### 1.2.0

*Jul 14, 2023*

* Added conversions to/from Microsoft GUID byte layout
* `Rfc4122Variant1Uuid` renamed to `Rfc4122Variant10xxUuid` (`Rfc4122Variant1Uuid` kept as an alias)

### 1.1.0

*Jul 8, 2023*

* `Rfc4122Uuid` now includes Max and Nil
* `Rfc4122Variant1Uuid` is added to contain only versions 1-8

### 1.0.0

*Jul 8, 2023*

* Support for 32-bit systems

## 0.x

### 0.2.0

*Jul 7, 2023*

* Changed internal representation to flat hex
* Fixed UUIDv7 generation for negative Unix timestamps
* The library no longer throws ValueError
* Added fromHex() and toHex()
* Timestamp on ULID & UUIDv7 is interpreted as unsigned
* Extract UlidParser and UlidFactory

### 0.1.0

*Jul 5, 2023*

* Initial release
