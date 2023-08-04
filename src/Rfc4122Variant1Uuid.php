<?php

// @codeCoverageIgnoreStart


declare(strict_types=1);

namespace Arokettu\Uuid;

class_alias(Rfc4122Variant10xxUuid::class, Rfc4122Variant1Uuid::class);

if (false) {
    /**
     * @deprecated
     */
    interface Rfc4122Variant1Uuid extends Rfc4122Uuid
    {
        public function getRfc4122Version(): int;
    }
}


// @codeCoverageIgnoreEnd
