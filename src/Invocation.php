<?php

declare(strict_types=1);

namespace Xtompie\Aop;

class Invocation
{
    /**
     * @param Aspect[] $aspects
     * @param string $joinpoint
     * @param callable $main
     * @param array $args
     */
    public function __construct(
        protected array $aspects,
        protected string $joinpoint,
        protected $main,
        protected array $args,
    ) {
    }

    public function __invoke(): mixed
    {
        if ($this->aspects) {
            $new = clone $this;
            $aspect = array_pop($new->aspects);
            return $aspect->advice($new);
        }

        return ($this->main)(...$this->args);
    }

    public function joinpoint(): string
    {
        return $this->joinpoint;
    }

    public function args(): array
    {
        return $this->args;
    }

    public function withArgs(array $args)
    {
        $new = clone $this;
        $new->args = $args;
        return $new;
    }

    public function hash(): string
    {
        return sha1(serialize([$this->joinpoint(), $this->args()]));
    }
}

