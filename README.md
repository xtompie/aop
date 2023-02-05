
# Aop - Aspect-Orientend Programing for PHP

This library require changes in the original source code.

For Symfony there is a bundle [xtompie/aop-symfony](https://github.com/xtompie/aop-symfony/)

In simplified and generalized term AOP is Publish/Subscribe system with Middlewares.

## Requiments

PHP >= 8.0

## Installation

Using [composer](https://getcomposer.org/)

```
composer require xtompie/aop
```

## Docs

### 1. Creating Aspect

Aspect is a class implementing `Aspect` interface.

```php
<?php

namespace Xtompie\Aop;

interface Aspect
{
    public function order(): float;
    public function joinpoint(string $joinpoint): bool;
    public function advice(Invocation $invocation): mixed;
}
```

Library provides abstract class `GenericAspect`.

```php
<?php

use Xtompie\Aop\GenericAspect;

class DebugAspect implements GenericAspect
{
    protected function pointcuts(): array
    {
        return [
            'FoobarService::*',
        ];
    }

    public function advice(Invocation $invocation): mixed
    {
        $result = $invocation();
        var_dump([
            'AOP',
            'joinpoint' => $invocation->joinpoint(),
            'args' => $invocation->args(),
            'result' => $result,
        ]);
        return $result;
    }
}
```

Pointcut is a pattern that can match Joinpoint.
Pointcut can have a `*` character that describes any character in any number of occurrences.
If Pointcut dont have `*` it is equal to Joinpoint.

There is only one type of Advice - around. Before and after can be achive manualy or using `GenericAspect`.

### 2. Create AOP system

q```php
<?php

use Xtompie\Aop\GenericAspect;

$aop = new Aop([
    new DebugAspect(),
]);

function aop(string $joinpoint, array $args, callable $main): mixed
{
    return $GLOBALS['aop']->__invoke($joinpoint, $args, $main);
}
```

### 3. Create joinpoint in service

```php
<?php

class FoobarService
{
    public function baz(int $a): int
    {
        return aop(__METHOD__, func_get_args(), function(int $a) { // <-- added line
            return $a +1;
        }); // <-- added line
    }
}
```

### 4. Changing invocation arguments

```php
<?php

use Xtompie\Aop\GenericAspect;

class AddFiveAspect implements GenericAspect
{
    protected function pointcuts(): array
    {
        return [
            'FoobarService::__invoke',
        ];
    }

    public function advice(Invocation $invocation): mixed
    {
        $invocation = $invocation->withArgs([$invocation->args()[0] + 5]);
        return $invocation();
    }
}
```

### 5. Changing aspect orders

The higher the order, the closer it is to the main invocation.

```php
<?php

use Xtompie\Aop\GenericAspect;

class AddFiveAspect implements GenericAspect
{
    public function order(): float
    {
        return 10;
    }

    // ...
}
```

### 6. Replace orginal invocation

Dont call invocation chain `$invocation()`.

```php
<?php

use Xtompie\Aop\GenericAspect;

class MinusTeenAspect implements GenericAspect
{
    public function order(): float
    {
        return 999;
    }

    protected function pointcuts(): array
    {
        return [
            'FoobarService::__invoke',
        ];
    }

    public function advice(Invocation $invocation): mixed
    {
        return $invocation->args()[0] - 10;
    }
}
```