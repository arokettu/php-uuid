<?php

/**
 * @copyright 2023 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

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
