<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Namespaces;

interface NamespaceInterface
{
    public function getBytes(): string;
}
