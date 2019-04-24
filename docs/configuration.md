I will use, as an example, a simple WordPress plugin project for which I want to write [PhpUnit](https://phpunit.de/ "PHPUnit – The PHP Testing Framework") unit tests; Function Mocker has been installed using [Composer](https://getcomposer.org/).  
The setup is common and generic enough but for more thorough or specific setup guides check out the [Different Setups page](/setups/index.md).  
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

use tad\FucntionMocker\FunctionMocker as the_function;

class FirstTestCase extends \PHPUnit\Framework\TestCase {

	public function setUp(){
		the_function::setUp();
	}

	public function test_stubbing_a_function(){
		// stub the `get_option` function
		the_function::get_option('foo')->willReturn('bar');

		$this->assertEquals('bar', get_option('foo'));
	}

	public function test_mocking_a_function(){
		// mock the `get_option` function to check if and how it's called
		the_function::get_option('foo')->shouldBeCalled();
		the_function::get_option('not-foo')->shouldNotBeCalled();

		$this->assertEquals('bar', get_option('foo'));
	}

	public function test_spying_a_function(){
		// spy the `get_option` function
		the_function::spy('get_option');
	
		// call it
		get_option('foo');
	
		// verify how it was called
		the_function::get_option('foo')->shouldHaveBeenCalled();
		the_function::get_option('not-foo')->shouldNotBeenCalled();
	}

	public function tearDown(){
		the_function::tearDown($this);
	}
}
```
 
 If you need to set up Function Mocker in a less standard environment [look into specific setup guides](/setups/index.md).
