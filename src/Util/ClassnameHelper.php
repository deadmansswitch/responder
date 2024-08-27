<?php

declare(strict_types=1);

namespace DeadMansSwitch\Responder\Util;

use Symfony\Component\Finder\SplFileInfo;

/**
 * @internal
 */
final class ClassnameHelper
{
    public static function getFqcnFromSplFileInfo(SplFileInfo $file): ?string
    {
        $ns = self::extract($file->getRealPath());
        if ($ns === null) {
            return null;
        }

        $name = $file->getBasename('.php');
        $fqcn = "{$ns}\\{$name}";
        if (class_exists($fqcn) === false) {
            return null;
        }

        return $fqcn;
    }

    public static function extract(string $path): ?string
    {
        $content = file_get_contents($path);
        $tokens  = token_get_all($content);
        $count   = count($tokens);

        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];

            if (!is_array($token)) {
                continue;
            }

            if ($token[0] !== T_NAMESPACE) {
                continue;
            }

            return $tokens[$i + 2][1];
        }

        return null;
    }
}