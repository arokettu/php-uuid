Upgrade
#######

.. highlight:: php

2.x to 3.0
==========

* Interface changes:

  * ``Rfc4122Variant10xxUuid`` was renamed to ``Variant10xxUuid`` and it no longer extends ``Rfc4122Uuid``
  * ``UuidV2``, ``UuidV6``, ``UuidV7``, ``UuidV8``, ``MaxUuid`` no longer implement ``Rfc4122Uuid``

    * If you strictly check for standard UUIDs, use ``Rfc9562Uuid``
    * ``UuidV2`` is no longer considered RFC-based UUID because neither RFC explains them
* V3/V5 namespaces were moved to the ``Arokettu\Uuid\Namespaces\UuidNamespace`` enum::

    <?php

    // v2:
    use Arokettu\Uuid\UuidFactory;
    use Arokettu\Uuid\UuidNamespaces;

    $uuid = UuidFactory::v5(UuidNamespaces::url(), 'http://example.com');
    var_dump($uuid->toString()); // 8c9ddcb0-8084-5a7f-a988-1095ab18b5df

    // v3:
    use Arokettu\Uuid\Namespaces\UuidNamespace;
    use Arokettu\Uuid\UuidFactory;

    $uuid = UuidFactory::v5(UuidNamespace::URL, 'http://example.com');
    var_dump($uuid->toString()); // 8c9ddcb0-8084-5a7f-a988-1095ab18b5df

* ``UuidV7Sequence`` renamed to ``UuidV7ShortSequence``

1.x to 2.0
==========

* Deprecated interface was removed::

    <?php

    use Arokettu\Uuid\Rfc4122Variant10xxUuid;
    use Arokettu\Uuid\Rfc4122Variant1Uuid

    if ($uuid instanceof Rfc4122Variant1Uuid) {
        // ...
    }

    // replace with

    if ($uuid instanceof Rfc4122Variant10xxUuid) {
        // ...
    }

* Deprecated sequences were removed::

    <?php

    use Arokettu\Uuid\SequenceFactory;
    use Arokettu\Uuid\UlidFactory;
    use Arokettu\Uuid\UuidFactory;

    // UUIDv7:

    $seq = UuidFactory::v7Sequence();

    // replace with

    $seq = SequenceFactory::v7();

    // ULID:

    $seq = UlidFactory::sequence();

    // replace with

    $seq = SequenceFactory::ulid();
