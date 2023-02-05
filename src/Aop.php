<?php

declare(strict_types=1);

namespace Xtompie\Aop;

use Xtompie\Sorter\Sort;
use Xtompie\Sorter\Sorter;

class Aop
{
    public function __construct(
        protected iterable $aspects,
        protected array $joinpoints = [],
    ) {
    }

    public function __invoke(string $joinpoint, array $args, callable $main): mixed
    {
        if (!array_key_exists($joinpoint, $this->joinpoints)) {
            $this->joinpoints[$joinpoint] = $this->aspects($joinpoint);
        }

        if (!$this->joinpoints[$joinpoint]) {
            return $main(...$args);
        }

        return (new Invocation(
            aspects: $this->joinpoints[$joinpoint],
            joinpoint: $joinpoint,
            main: $main,
            args: $args
        ))();
    }

    protected function aspects(string $joinpoint): array
    {
        $aspects = [];
        foreach ($this->aspects as $aspect) {
            /** @var Aspect $aspect */
            if ($aspect->joinpoint($joinpoint)) {
                $aspects[] = $aspect;
            }
        }

        $aspects = (new Sorter())->__invoke(
            [Sort::of(fn (Aspect $aspect) => $aspect->order())],
            $aspects
        );

        return $aspects;
    }
}


