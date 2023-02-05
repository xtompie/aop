<?php

declare(strict_types=1);

namespace Xtompie\Aop;

interface Aspect
{
    public function order(): float;

    public function joinpoint(string $joinpoint): bool;

    public function advice(Invocation $invocation): mixed;
}