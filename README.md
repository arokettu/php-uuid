# PHP UUID Library

[![Packagist]][Packagist Link]
[![PHP]][Packagist Link]
[![License]][License Link]
[![Gitlab CI]][Gitlab CI Link]
[![Codecov]][Codecov Link]

[Packagist]: https://img.shields.io/packagist/v/arokettu/uuid.svg?style=flat-square
[PHP]: https://img.shields.io/packagist/php-v/arokettu/uuid.svg?style=flat-square
[License]: https://img.shields.io/packagist/l/arokettu/uuid.svg?style=flat-square
[Gitlab CI]: https://img.shields.io/gitlab/pipeline/sandfox/php-uuid/master.svg?style=flat-square
[Codecov]: https://img.shields.io/codecov/c/gl/sandfox/php-uuid?style=flat-square

[Packagist Link]: https://packagist.org/packages/arokettu/uuid
[License Link]: LICENSE.md
[Gitlab CI Link]: https://gitlab.com/sandfox/php-uuid/-/pipelines
[Codecov Link]: https://codecov.io/gl/sandfox/php-uuid/

UUID and ULID classes for PHP.

## Installation

```bash
composer require arokettu/uuid
```

* Either GMP or Bcmath extension is strongly recommended on 32-bit systems.

## Usage

```php
<?php

use Arokettu\Uuid\DceSecurity\Domains;
use Arokettu\Uuid\Namespaces\UuidNamespace;
use Arokettu\Uuid\UlidFactory;
use Arokettu\Uuid\UlidParser;
use Arokettu\Uuid\UuidFactory;
use Arokettu\Uuid\UuidParser;

// create UUIDs versions 1-8 and ULIDs
$uuid1 = UuidFactory::v1(); // example: 9a289f42-87ca-11ee-9f2b-41a3b4016a63
$uuid2 = UuidFactory::v2(Domains::PERSON, 1234); // example: 000004d2-ab4c-21ee-9700-47774c7dcdc1
$uuid3 = UuidFactory::v3(UuidNamespace::URL, 'http://example.com/'); // 773536a8-4b7b-383d-9106-697d4d366254
$uuid4 = UuidFactory::v4(); // example: 5c24b036-6202-419f-a1f3-48cbe6ebf17a
$uuid5 = UuidFactory::v5(UuidNamespace::URL, 'http://example.com/'); // 0a300ee9-f9e4-5697-a51a-efc7fafaba67
$uuid6 = UuidFactory::v6(); // example: 1ee771bd-9fb2-6000-b969-1334567890ab
$uuid7 = UuidFactory::v7(); // example: 01892370-4c48-70cf-9cb9-96784308f504
$ulid  = UlidFactory::ulid(); // example: 01H4HQC4G1C1606J19358PWESA

// get data like timestamps on UUIDs versions 1, 2, 6, 7 and ULIDs
$uuid7->getDateTime(); // 2023-07-05 00:25:09.448 +00:00

// parse existing UUID or ULID
$uuid  = UuidParser::fromString('01892370-4c48-70cf-9cb9-96784308f504'); // == $uuid7
$ulid2 = UlidParser::fromString('01H4HQC4G1C1606J19358PWESA'); // == $ulid

// possible killer features
// UUIDv1 to UUIDv6 conversion (and vice versa)
UuidParser::fromString('e982dc4e-1acc-11ee-be56-0242ac120002')
    ->toUuidV6(); // 1ee1acce-982d-6c4e-be56-0242ac120002
// ULID to UUIDv7 conversion (lossy but predictable)
UlidParser::fromString('01H4HQC4G1C1606J19358PWESA')
    ->toUuidV7(lossy: true); // 01892376-1201-704c-8348-2919516e3b2a
```

## Documentation

Read full documentation here: <https://sandfox.dev/php/uuid.html>

Also on Read the Docs: <https://arokettu-uuid.readthedocs.io/>

## Support

Please file issues on our main repo at GitLab: <https://gitlab.com/sandfox/php-uuid/-/issues>

Feel free to ask any questions in our room on Gitter: <https://gitter.im/arokettu/community>

## License

The library is available as open source under the terms of the [MIT License][License Link].
