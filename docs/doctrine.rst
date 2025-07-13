Doctrine Support
################

.. highlight:: php

|Packagist| |GitLab| |GitHub| |Codeberg| |Gitea|

Doctrine support is split into a separate package.

Installation
============

.. code-block:: bash

   composer require 'arokettu/uuid-doctrine'

Available Types
===============

* ``UuidType``. UUID stored in native GUID if available or CHAR(36), same as GUID type in Doctrine.
* ``UuidBinaryType``. UUID stored in BINARY(16).
* ``UlidType``. ULID stored in CHAR(26).
* ``UlidBinaryType``. ULID stored in BINARY(16).

Available Generators
====================

* ``UuidV4Generator``. UUIDv4 generator.
* ``UuidV7Generator``. UUIDv7 generator. Uses a monotonic sequence internally.
* ``UlidGenerator``. ULID generator. Uses a monotonic sequence internally.

Usage
=====

Register types::

    <?php

    use Arokettu\Uuid\Doctrine\{UlidBinaryType,UlidType,UuidBinaryType,UuidType};
    use Doctrine\DBAL\Types\Type;

    // registers types directly
    Type::addType(UuidType::NAME, UuidType::class);
    Type::addType(UuidBinaryType::NAME, UuidBinaryType::class);
    Type::addType(UlidType::NAME, UlidType::class);
    Type::addType(UlidBinaryType::NAME, UlidBinaryType::class);

.. note:: See your framework documentation for proper configuration of custom Doctrine types.

Apply type to a model::

    <?php

    use Arokettu\Uuid\Doctrine\{UuidType,UuidV4Generator};
    use Arokettu\Uuid\Uuid;
    use Doctrine\ORM\Mapping\{Column,CustomIdGenerator,Entity,GeneratedValue,Id,Table};

    #[Entity, Table(name: 'uuid_object')]
    class UuidObject
    {
        #[Column(type: UuidType::NAME)]
        #[Id, GeneratedValue(strategy: 'CUSTOM'), CustomIdGenerator(UuidV4Generator::class)]
        public Uuid $id;

        #[Column(type: UuidType::NAME)]
        public Uuid $uuidString;
    }

.. |Packagist|  image:: https://img.shields.io/packagist/v/arokettu/uuid-doctrine.svg?style=flat-square
   :target:     https://packagist.org/packages/arokettu/uuid-doctrine
.. |GitHub|     image:: https://img.shields.io/badge/get%20on-GitHub-informational.svg?style=flat-square&logo=github
   :target:     https://github.com/arokettu/uuid-doctrine
.. |GitLab|     image:: https://img.shields.io/badge/get%20on-GitLab-informational.svg?style=flat-square&logo=gitlab
   :target:     https://gitlab.com/sandfox/uuid-doctrine
.. |Codeberg|   image:: https://img.shields.io/badge/get%20on-Codeberg-informational.svg?style=flat-square&logo=codeberg
   :target:     https://codeberg.org/sandfox/uuid-doctrine
.. |Gitea|      image:: https://img.shields.io/badge/get%20on-Gitea-informational.svg?style=flat-square&logo=gitea
   :target:     https://sandfox.org/sandfox/uuid-doctrine
