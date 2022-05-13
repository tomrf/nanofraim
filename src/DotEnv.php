<?php

declare(strict_types=1);

namespace Nanofraim;

class DotEnv
{
    public static function loadDotEnv(string $path, bool $overwriteExisting = false): void
    {
        if (!file_exists($path)) {
            return;
        }

        $env = parse_ini_file($path, true, INI_SCANNER_TYPED);

        foreach ($env as $key => $value) {
            if (false === $overwriteExisting && isset($_ENV[$key])) {
                continue;
            }
            $_ENV[$key] = $value;
        }
    }
}
