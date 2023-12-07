# Changelog

## 2.x

### 2.0.0

*Dec 7, 2023*

Forked from 1.4.5

* Removed deprecations:
  * `Rfc4122Variant10xxUuid`
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
