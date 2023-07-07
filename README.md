# PHP UUID Library

[![Packagist](https://img.shields.io/packagist/v/arokettu/uuid.svg?style=flat-square)](https://packagist.org/packages/arokettu/uuid)
[![PHP](https://img.shields.io/packagist/php-v/arokettu/uuid.svg?style=flat-square)](https://packagist.org/packages/arokettu/uuid)
[![License](https://img.shields.io/packagist/l/arokettu/uuid.svg?style=flat-square)](LICENSE.md)
[![Gitlab pipeline status](https://img.shields.io/gitlab/pipeline/sandfox/php-uuid/master.svg?style=flat-square)](https://gitlab.com/sandfox/php-uuid/-/pipelines)
[![Codecov](https://img.shields.io/codecov/c/gl/sandfox/php-uuid?style=flat-square)](https://codecov.io/gl/sandfox/php-uuid/)

UUID and ULID classes for PHP.

## Usage

```php
<?php

use Arokettu\Uuid\UlidFactory;
use Arokettu\Uuid\UlidParser;
use Arokettu\Uuid\UuidFactory;
use Arokettu\Uuid\UuidNamespaces;
use Arokettu\Uuid\UuidParser;

// create UUIDs versions 3, 4, 5, 7, 8 and ULIDs
$uuid4 = UuidFactory::v4(); // example: 5c24b036-6202-419f-a1f3-48cbe6ebf17a
$uuid5 = UuidFactory::v5(UuidNamespaces::url(), 'http://example.com/'); // 0a300ee9-f9e4-5697-a51a-efc7fafaba67
$uuid7 = UuidFactory::v7(); // example: 01892370-4c48-70cf-9cb9-96784308f504
$ulid  = UlidFactory::ulid(); // example: 01H4HQC4G1C1606J19358PWESA

// get data like timestamps on UUIDs versions 1, 2, 6, 7 and ULIDs
$uuid7->getDateTime(); // 2023-07-05 00:25:09.448 +00:00

// parse existing UUID or ULID
$uuid  = UuidParser::fromString('01892370-4c48-70cf-9cb9-96784308f504'); // == $uuid7
$ulid2 = UlidParser::fromString('01H4HQC4G1C1606J19358PWESA'); // == $uuid

// possible killer features
// UUIDv1 to UUIDv6 conversion (and vice versa)
UuidParser::fromString('e982dc4e-1acc-11ee-be56-0242ac120002')
    ->toUuidV6(); // 1ee1acce-982d-6c4e-be56-0242ac120002
// ULID to UUIDv7 conversion (lossy but predictable)
UlidParser::fromString('01H4HQC4G1C1606J19358PWESA')
    ->toUuidV7(lossy: true); // 01892376-1201-704c-8348-2919516e3b2a
```

## Installation

```bash
composer require arokettu/uuid
```

## Limitations

* 32 bit is not supported yet (will be by 1.0.0)

## Documentation

Documentation is a sort of work in progress.

Read full documentation here: <https://sandfox.dev/php/uuid.html>

Also on Read the Docs: <https://arokettu-uuid.readthedocs.io/>

## Support

Please file issues on our main repo at GitLab: <https://gitlab.com/sandfox/php-uuid/-/issues>

Feel free to ask any questions in our room on Gitter: <https://gitter.im/arokettu/community>

## License

The library is available as open source under the terms of the [MIT License](LICENSE.md).
