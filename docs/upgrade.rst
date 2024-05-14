Upgrade
#######

.. highlight:: php

2.x to 3.0
==========

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
