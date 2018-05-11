---
title: PHPUnit Setup
url: /setups/phpunit.html
permalink: /setups/phpunit.html
sidebar_link: false
---

## Installation
If you did not already require [PhpUnit](https://phpunit.de/ "PHPUnit – The PHP Testing Framework")	as a developer dependency for your project:
```bash
composer require phpunit/phpunit --dev
```

Require Function Mocker as a developer dependency using [Composer](https://getcomposer.org/):
```bash
composer require lucatume/function-mocker --dev
```

## Setup
If you did not already generate a PHPUnit configuration file, generate one using PHPUnit built-in command:
```bash
vendor/bin/phpunit 
```

By default PHPUnit will use the `vendor/autoload.php` file as bootstrap file; Function Mocker requires its own bootstrap phase so change the PHPUnit configuration file to use a different bootstrap file, e.g. `tests/bootstrap.php`:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/6.5/phpunit.xsd"
         bootstrap="bootstrap.php">
    <testsuite name="default">
        <directory suffix="Test.php">.</directory>
    </testsuite>
</phpunit>
```

In the `bootstrap.php` file initialize Function Mocker specifying the configuration it should use:

```php
<?php
require_once  __DIR__ . '/vendor/autoload.php';

// init Function Mocker
\tad\FunctionMocker\FunctionMocker::init( [
	'cache-path'            => __DIR__ . '/tests/_cache',
	'whitelist'             => [ __DIR__ . '/src', __DIR__ . '/tests'  ],
	'redefinable-internals' => [ 'time' ],
] );
```

[Read more about Function Mocker initialization configuration](/setups/configuration.html).

## Usage
In any test case (a class extending the `PHPUnit\Framework\TestCase` class) where you would like to use Function Mocker you should call the `FunctionMocker::setUp` method in the test case `setUp` method and the `FunctionMocker::tearDown` method in the `tearDown` method.
