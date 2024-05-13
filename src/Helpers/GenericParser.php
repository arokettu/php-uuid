<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

use Arokettu\Uuid\AbstractParser;
use Arokettu\Uuid\GenericUuid;
use DomainException;

/**
 * @psalm-api
 * @internal
 * @extends AbstractParser<GenericUuid>
 */
final class GenericParser extends AbstractParser
{
    protected const TYPE = 'UUID';

    public static function fromHex(string $hex): GenericUuid
    {
        if (preg_match('/^[0-9a-f]{32}$/i', $hex) !== 1) {
            throw new DomainException('UUID must be 32 hexadecimal digits');
        }

        return new GenericUuid(strtolower($hex));
    }
}
