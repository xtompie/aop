<?php

declare(strict_types=1);

namespace Xtompie\Aop;

abstract class GenericAspect implements Aspect
{
    public function order(): float
    {
        return 0;
    }

    public function joinpoint(string $joinpoint): bool
    {
        return Pointcut::match($joinpoint, $this->pointcuts());
    }

    protected function pointcuts(): array
    {
        return [];
    }

    public function advice(Invocation $invocation): mixed
    {
        $invocation = $this->before($invocation);
        $result = $invocation();
        $result = $this->after($invocation, $result);
        return $result;
    }

    protected function before(Invocation $invocation)
    {
        return $invocation;
    }

    protected function after(Invocation $invocation, mixed $result): mixed
    {
        return $result;
    }
}
