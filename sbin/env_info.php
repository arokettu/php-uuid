<?php

declare(strict_types=1);

echo 'Bits: ', PHP_INT_SIZE * 8, PHP_EOL;
echo 'GMP: ', \extension_loaded('gmp') ? 'Yes' : 'No', PHP_EOL;
echo 'Bcmath: ', \extension_loaded('bcmath') ? 'Yes' : 'No', PHP_EOL;
