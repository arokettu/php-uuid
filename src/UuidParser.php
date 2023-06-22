<?php

namespace Arokettu\Uuid;

final class UuidParser
{
    public static function fromBytes(string $bytes): BaseUuid
    {
        if (\strlen($bytes) !== 16) {
            throw new \ValueError('UUID must be 16 bytes long');
        }

        if (Helpers\UuidBytes::getVariant($bytes) === 1) {
            return match (Helpers\UuidBytes::getVersion($bytes)) {
                1 => new UuidV1($bytes),
//                2 => new GenericUuid($bytes), // todo: support
                3 => new UuidV3($bytes),
                4 => new UuidV4($bytes),
                5 => new UuidV5($bytes),
//                6 => new GenericUuid($bytes), // todo: support
                7 => new UuidV7($bytes),
                8 => new UuidV8($bytes),
                default => new GenericUuid($bytes),
            };
        }

        if ($bytes === NilUuid::BYTES) {
            return new NilUuid();
        }
        if ($bytes === MaxUuid::BYTES) {
            return new MaxUuid();
        }

        return new GenericUuid($bytes);
    }
}
