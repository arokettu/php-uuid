<?php

namespace Arokettu\Uuid\Sequences;

use Arokettu\Uuid\Uuid;
use IteratorAggregate;

/**
 * @template T of Uuid
 * @extends IteratorAggregate<T>
 */
interface UuidSequence extends IteratorAggregate
{
    /**
     * @return T
     */
    public function next(): Uuid;
}
