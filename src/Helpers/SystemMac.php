<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

/**
 * @internal
 */
final class SystemMac
{
    private static string $mac;

    public static function get(): string
    {
        // do not run the command multiple times in the same process
        return self::$mac ??= self::determine();
    }

    public static function determine(): string
    {
        $mac = shell_exec('getmac');

        if ($mac === null) {
            throw new \RuntimeException('Unable to determine system MAC address');
        }

        return trim($mac);
    }
}
