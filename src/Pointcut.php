<?php

declare(strict_types=1);

namespace Xtompie\Aop;

class Pointcut
{
    public static function match(string $joinpoint, array $pointcuts): bool
    {
        foreach ($pointcuts as $pointcut) {
            $pointcut = preg_quote($pointcut, '#');
            $pointcut = str_replace('\*', '.*', $pointcut);
            $pointcut = "#^$pointcut$#";
            if (1 === preg_match($pointcut, $joinpoint)) {
                return true;
            }
        }

        return false;
    }
}
