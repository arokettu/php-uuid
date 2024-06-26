.. _uuidv1nodes:

Node ID Generators
##################

.. highlight:: php

Main interface: ``Arokettu\Uuid\Node\Node``

Node generator classes represent different algorithms to generate
the node field of the UUIDv1 and UUIDv6 as described in the `RFC 9562`_.
Node ID is a 48 bit long value, same as the MAC address.

Random Node
===========

``Arokettu\Uuid\Node\RandomNode``

Random node is a wrapper for a random generator, it will return a new byte sequence for every new UUID.
This is the default node generator for the factory.

As described in the `section 6.10`_ for non-MAC node IDs, the multicast bit will be set in the generated value.

::

    <?php

    use Arokettu\Uuid\Node\RandomNode;
    use Arokettu\Uuid\UuidFactory;
    use Random\Engine\PcgOneseq128XslRr64;
    use Random\Randomizer;

    // RNG can be overridden
    $node = new RandomNode(
        new Randomizer(new PcgOneseq128XslRr64(111)) // generates d2f8f1d31aaa
    );
    $uuid = UuidFactory::v6($node); // ........-....-....-....-d3f8f1d31aaa
    $uuid = UuidFactory::v6($node); // ........-....-....-....-bdcc9a006b19

Static Node
===========

``Arokettu\Uuid\Node\StaticNode``

Static node holds a constant value.
It can be generated from any 6 byte value.
This is the default generator for the sequences.

Factories:

``StaticNode::fromBytes()``
    to generate a node ID from any 6 bytes
``StaticNode::fromHex()``
    to generate a node ID from data in hex form (12 digits)
``StaticNode::random()``
    to generate a random node ID

Like a random node, static nodes have multicast bit set.

MAC Node
========

.. versionadded:: 2.2 ``MacNode::trySystem()``

``Arokettu\Uuid\Node\MacNode``

This is a node containing a MAC address (MAC-48/EUI-48).
This was the default node algorithm proposed by the `RFC 4122`_ but it is no longer recommended.

Factories:

``MacNode::parse()``
    Parses a known MAC address.
    The factory accepts Unix-style MAC notation (``12:34:56:78:90:ab``),
    Windows-style MAC notation (``12-34-56-78-90-ab``),
    and a hex string without any separators (``1234567890ab``)
``MacNode::system()``
    Tries to determine the system MAC address.
    This method works only on Linux and may be unreliable.
``MacNode::trySystem()``
    Tries to determine the system MAC address and returns null on failure.
    It can be used with a safe fallback::

        <?php

        $mac = MacNode::trySystem() ?? StaticNode::random();

Raw Node
========

``Arokettu\Uuid\Node\RawNode``

Like static node but without multicast bit validator.
It is user's responsibility to follow the standard with this type.

``RawNode::fromBytes()``
    to generate a node ID from any 6 bytes
``RawNode::fromHex()``
    to generate a node ID from data in hex form (12 digits)

.. _RFC 4122: https://datatracker.ietf.org/doc/html/rfc4122
.. _RFC 9562: https://datatracker.ietf.org/doc/html/rfc9562
.. _section 6.10: https://datatracker.ietf.org/doc/html/rfc9562#section-6.10
