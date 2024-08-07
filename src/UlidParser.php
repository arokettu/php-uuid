<?php

declare(strict_types=1);

namespace Arokettu\Uuid;

use UnexpectedValueException;

/**
 * @psalm-api
 * @extends AbstractParser<Ulid>
 */
final class UlidParser extends AbstractParser
{
    protected const TYPE = 'ULID';

    public static function fromHex(string $hex): Ulid
    {
        if (preg_match('/^[0-9a-f]{32}$/i', $hex) !== 1) {
            throw new UnexpectedValueException('ULID must be 32 hexadecimal digits');
        }

        return new Ulid(strtolower($hex));
    }
}
