---
title: Quick start
url: quickstart.html
permalink: quickstart.html
sidebar_link: true
---

## Installation
Function Mocker should be installed as a developer dependency using [Composer](https://getcomposer.org/); from your WordPress project root folder (a plugin, a theme or a whole site) run:

```bash
composer install --dev lucatume/function-mocker
```

Function Mocker has two main dependencies and it will pull them when installing it:

* [Patchwork](!g patchwork2 php) - a monkey patching library for PHP
* [Prophecy](!g prophecy php) - [phpspec](!g) own mocking engine

If you are using [PHPUnit](https://phpunit.de/ "PHPUnit – The PHP Testing Framework") the second will be installed with it, still it's good to know what is happening.

## Setup
I will use, as an example, a simple WordPress plugin project for which I want to write [PhpUnit](https://phpunit.de/ "PHPUnit – The PHP Testing Framework") unit tests; Function Mocker has been installed using [Composer](https://getcomposer.org/).  
The setup is common and generic enough but for more thorough or specific setup guides check out the [Different Setups page](/different-setups.html).  
Assuming PHPUnit will use the `tests/bootstrap.php` file to bootstrap you need to initialize Function Mocker:

```php
// file tests/bootstrap.php

require_once __DIR__ . '/../vendor/autoload.php'

\tad\FunctionMocker\FunctionMocker::init([

	// Patchwork will need to pre-process the files before running
	// this can be a long and costly operation, especially in integration tests
	// specifying a cache directory will allow Patchwork to run only once
	'cache-path' => __DIR__ . '/_cache',

	// a list of PHP internal functions that might be redefined in the tests
	'redefinable-internals'	=> [
		'time',
	]
]);
```

In any test case that will use Function Mocker you need to call the `FunctionMocker::setUp()` method before each test and the `FunctionMocker::tearDown()` method after each one:

```php
// file tests/FirstTestCase.php

use tad\FucntionMocker\FunctionMocker;

class FirstTestCase extends \PHPUnit\Framework\TestCase {

	public function setUp(){
		FunctionMocker::setUp();	
	}

	public function test_stubbing_a_function(){
		// stub the `get_option` function
		FunctionMocker::get_option('foo')->willReturn('bar');

		$this->assertEquals('bar', get_option('foo'));
	}

	public function test_mocking_a_function(){
		// mock the `get_option` function to check if and how it's called
		FunctionMocker::get_option('foo')->shouldBeCalled();
		FunctionMocker::get_option('not-foo')->shouldNotBeCalled();

		$this->assertEquals('bar', get_option('foo'));
	}

	public function test_spying_a_function(){
		// spy the `get_option` function
		FunctionMocker::spy('get_option');
	
		// call it
		get_option('foo');
	
		// verify how it was called
		FunctionMocker::get_option('foo')->shouldHaveBeenCalled();
		FunctionMocker::get_option('not-foo')->shouldNotBeenCalled();
	}

	public function tearDown(){
		FunctionMocker::tearDown($this);
	}
}
```

> If you
 use an IDE that will index the project files (e.g. PhpStorm) take care to exclude the cache folder from the indexing to avoid duplicated definitions.
 
 If you need to set up Function Mocker in a less standard environment [look into specific setup guides](/setups/index.md).