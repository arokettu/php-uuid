<?php

/**
 * @copyright 2023 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Uuid;

use UnexpectedValueException;

/**
 * @psalm-api
 * @extends AbstractParser<Uuid>
 */
final class UuidParser extends AbstractParser
{
    protected const TYPE = 'UUID';

    public static function fromHex(string $hex): Uuid
    {
        if (preg_match('/^[0-9a-f]{32}$/i', $hex) !== 1) {
            throw new UnexpectedValueException('UUID must be 32 hexadecimal digits');
        }

        $hex = strtolower($hex);

        if (Helpers\UuidBytes::getVariant($hex) === Helpers\UuidVariant::v10xx) {
            return match (Helpers\UuidBytes::getVersion($hex)) {
                1 => new UuidV1($hex),
                2 => new UuidV2($hex),
                3 => new UuidV3($hex),
                4 => new UuidV4($hex),
                5 => new UuidV5($hex),
                6 => new UuidV6($hex),
                7 => new UuidV7($hex),
                8 => new UuidV8($hex),
                default => new GenericUuid($hex),
            };
        }

        if ($hex === NilUuid::HEX) {
            return new NilUuid();
        }
        if ($hex === MaxUuid::HEX) {
            return new MaxUuid();
        }

        return new GenericUuid($hex);
    }
}
