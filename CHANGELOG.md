# Changelog

## 1.x

### next

* Factory methods to generate UUIDv1 and UUIDv6
* Speed up non-monotonic generation of UUIDv7 and ULID

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
