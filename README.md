# Function Mocker

*A [Patchwork](http://antecedent.github.io/patchwork/) powered function mocker born to make WordPress unit-testing easier.*

In a perfect world you should never need to mock static methods and functions, should use [TDD](http://en.wikipedia.org/wiki/Test-driven_development) to write better object-oriented code and use tests as a code design tool.  
But sometimes a grim and sad need to mock those functions and static methods might arise when working with code that has a lot of miles under its belt; this library is here to help.

[![Build Status](https://travis-ci.org/lucatume/function-mocker.svg?branch=master)](https://travis-ci.org/lucatume/function-mocker)

## Show me the code
This can be written in a [PHPUnit](http://phpunit.de/) test suite, given this class:
    
```php
// the class under test
// it uses the WordPress `get_option` and `update_option` functions
class Logger {
    public function log( $type, $message ) {
        $option = get_option( 'log' );
        $option[] = sprintf( '[%s] %s - %s', date(DATE_ATOM, time() ), $type, $message );
        update_option( 'log', sprintf('[%s] %s - %s', date( DATE_ATOM, time() ), $type, $message ));
    }
}
```

This is a working test case for it:

```php
// the test case for the class

use \tad\FunctionMocker\FunctionMocker;

class InternaFunctionReplacementTest extends \PHPUnit\Framework\TestCase {
	
	public function setUp() {
		FucntionMocker::setUp();
	}
	
    public function test_it_logs_the_correct_message() {
        // replace the `time` internal function
        $mockTime = time();
        FunctionMocker::time()->willReturn( $mockTime );
        
        // stub the `get_option` function
        FunctionMocker::get_option()->willReturn([]);
        
        // mock the `update_option` function
        $expected = sprintf('[%s] error - There was an error', date(DATE_ATOM, $mockTime));
        FunctionMocker::update_option( 'log', $expected )
            ->shouldBeCalled();

        $logger = new Logger();

        $logger->log( 'error', 'There was an error' );
    }
    
    public function tearDown() {
    	FunctionMocker::tearDown();
    }
}
```

TOC here

## How does it work?
Function Mocker leverages the power of [Patchwork](!g patchwork php) to allow user-land monkey patching.  
This means that Function Mocker is able to define non-existing functions and redefine existing functions without reuquiring the [runkit PHP extension](!g).  
Function Mocker will watch for any PHP file loaded (with `include` and `require` directives), modify it to add insertion code, and load that in place of the original file; this is ["monkey-patching"](!g).  
To work, then, Function Mocker needs to start **before** the code it should patch is loaded; since the patching is a cumbersome task Function Mocker requires a cache folder to store the result of that patching; when loading a small number of file the time is trivial but when larger codebases, e.g. WordPress, have to patched the overhead of the patching would be inconvenient.  
Once the patches are in place Function Mocker can start to alter the behaviour of the redefined functions for the purpose of testing.

## Installation
Use [Composer](https://getcomposer.org/) to require Function Mocker as a developer dependency:

    composer require lucatume/function-mocker:^2.0 --dev

## Usage
### Bootstrapping Function Mocker
To make Function Mocker behave in its wrapping power (a power granted by [patchwork](https://github.com/antecedent/patchwork)) the `FunctionMocker::init` method needs to be called in the tests bootstrap file.  
In this example I'm using [PHPUnit](http://phpunit.de/) but the `/examples` folder provides more working and complete setups covering [Codeception](http://codeception.com/ "Codeception - BDD-style PHP testing."), [Behat][2848-0001], [wp-browser](https://github.com/lucatume/wp-browser "lucatume/wp-browser · GitHub") and [phpspec][2848-0002].

```php
require_once dirname( __FILE__ ) . '/../vendor/autoload.php';

\tad\FunctionMocker\FunctionMocker::init([

	// Whitelist the folders and files FunctionMocker should "wrap"
    'whitelist' => [
		dirname(__DIR__) . '/src',
		dirname(__DIR__) . '/vendor',
    ],
    
    // Blacklist folders or files to avoid function mocker from wrapping them
    'blacklist' => [
		dirname(__DIR__) . '/src/includes', 
		dirname(__DIR__) . '/env.php', 
	 ],
	 
	 // When wrapping files Function Mocker will create a modified
     // copy of them the first time it "wraps" them,
     // set a cache path to control where the files should be stored.
	 // Assuming this file is in `/repos/my-project/tests`,
     // then the cache will be in `/repos/fm-cache/my-project`
    'cache-path' => __DIR__ . '/../../fm-cache/my-project',
    
    // Finally tell Function Mocker what internal PHP functions
    // it should allow you to mock, stub and spy in your tests
    'redefinable-internals' => ['time', 'filter_var']
]);
```

The `init` method will accept a configuration array supporting the following arguments:

* `whitelist` - array; a list of **absolute** paths to files and folders that should be wrapped and patched by Function Mocker; if a function is defined in a file that was loaded before Function Mocker is initialized or in a file that Function Mocker is not wrapping then it will not be available for stubbing, mocking or spying.
* `blacklist` - array; a list of **absolute** paths to files and folders that should be excluded in the monkey-patching; this is used to refine what files and folders Function Mocker should wrap.
* `cache-path` - string; the **absolute** path to the folder where Pathchwork should cache the patched files; to avoid double definitions in modern IDEs select a folder outside of your project folder or exclude the cache folder from indexing; **do not** use temporary folders like `/tmp` or `sys_get_temp_dir()`: Function Mocker cannot wrap those system folders in a reliable way, it *might* work but it's not a supported function. If a `cache-path` is not provided then Function Mocker will cache the files in the `/cache` folder in the package root folder.
* `redefinable-internals` - array; a list of internal PHP functions that will be available for redefinition in your tests; any function that is not listed here will not be redefinable in your tests.
* `env` - array; a list of [environment](#environment) files that should be loaded before the tests; read more in the dedicated section.

### setUp and tearDown methods  
After it's been initialized Function Mocker alteration to the functions behaviour need to be setup and torn down before and after each test method runs.  
I'm again, here, using an example based on [PhpUnit](https://phpunit.de/ "PHPUnit – The PHP Testing Framework")	but complete and working setups are available in the `/examples` folder of the repository.  
Following the initialization of Function Mocker in the tests bootstrap file, in the test case I'm calling the `FunctionMocker::setUp()` and `FunctionMocker::tearDown()` in the test case `setUp` and `tearDown` methods; the method name similarity is by design.  

```php
use \tad\FunctionMocker\FunctionMocker;

class MyTest extends \PHPUnit\Framework\TestCase {
    public function setUp(){
        // before any other set up method
        FunctionMocker::setUp();
        //...
    }

	// test methods here

    public function tearDown(){
        //...

        // after any other tear down method
        FunctionMocker::tearDown();
    }
}
```

## Creating, updating and using test environments
When initializing Function Mocker without specifying an `env` argument the default WordPress test environment will be loaded. 
The default WordPress test environment will define commonly used WordPress functions like `add_filter` or `__` that your code might rely on and just *assume* as defined and "working as usual".  
When using Function Mocker in the context of integration tests, where WordPress is loaded, testing environments should not be loaded to avoid double definition issues; defining the `env` parameter as an empty array will tell Function Mocker not to load any testing environments:

```php
FunctionMocker init without envs
```

The default WordPress testing environment defines a reduced and barebones set of functions and methods:

```
add_filter
add_action
do_action
apply_filters
did_action
__
_e
```

That might be enough for most unit tests but, should that not be the case, then Function Mocker comes with its own test environment generation CLI  tool.  

### An example WooCommerce environment
[See the example in the `examples/woocommerce-env` folder](/examples/woocommerce-env/README.md).

### Stubbing, mocking and spying function\
#### Stubbing functions
The library will allow for replacement of **defined and undefined** functions at test run time using the `FunctionMocker::replace` method like:

```php
FunctionMocker::replace('myFunction', $returnValue);
```

and will allow setting a return value as a real value or as a function callback:

```php    
public function testReplacedFunctionReturnsValue(){
    FunctionMocker::replace('myFunction', 23);

    $this->assertEquals(23, myFunction());
}

public fuction testReplacedFunctionReturnsCallback(){
    FunctionMocker::replace('myFunction', function($arg){
            return $arg + 1;
        });

    $this->assertEquals(24, myFunction());
}
```

#### Mocking functions
#### Spying functions
If the value returned by the `FunctionMocker::replace` method is stored in a variable than checks for calls can be made on that function:

```php  
public function testReplacedFunctionReturnsValue(){
    $myFunction = FunctionMocker::replace('myFunction', 23);

    $this->assertEquals(23, myFunction());

    $myFunction->wasCalledOnce();
    $myFunction->wasCalledWithOnce(23);
}
```

The methods available for a function spy are the ones listed below in the "Methods" section.

