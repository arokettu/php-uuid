<?php

declare(strict_types=1);

namespace Arokettu\Uuid\Helpers;

use FilesystemIterator;
use SplFileInfo;

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
        if (is_dir('/sys/class/net')) {
            $it = new FilesystemIterator('/sys/class/net', FilesystemIterator::SKIP_DOTS);

            $finfo = iterator_to_array($it);
            usort($finfo, function (SplFileInfo $a, SplFileInfo $b) {
                // search for the oldest created interface
                return $a->getMTime() <=> $b->getMTime();
            });

            foreach ($finfo as $f) {
                /** @var SplFileInfo $f */
                if ($f->getFilename() === 'lo') {
                    continue; // skip localhost
                }

                $mac = trim(@file_get_contents($f->getPathname() . '/address'));

                if ($mac) {
                    return $mac;
                }
            }
        }

        // @codeCoverageIgnoreStart
        // we can't test success and failure in the same process
        return '';
        // @codeCoverageIgnoreEnd
    }
}
