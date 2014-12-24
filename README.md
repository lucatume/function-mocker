j#Function Mocker

*A [Patchwork](http://antecedent.github.io/patchwork/) powered function mocker.*

## Show me the code
This can be written in a [PHPUnit](http://phpunit.de/) test suite

    use tad\FunctionMocker\FunctionMocker;

    class SomeClassTest extends \PHPUnit_Framework_TestCase {
        
        public function setUp(){
            FunctionMocker::setUp();
        }

        public function tearDown(){
            FunctionMocker::tearDown();
        }

        public function testSomeMethodCallsSomeFunction(){
            // Setup
            // please note: it can replace not defined functions too!
            $functionMock = FunctionMocker::replace('some_function');

            // Exercise
            some_function();

            // Assert
            $functionMock->wasCalledTimes(1);
        }

        public function testSomeMethodCallsSomeStaticMethod(){
            // Setup
            $staticMethod = FunctionMocker::replace('Post::get_post_title', 'Post title');

            // Exercise
            $this->assertEquals('Post title', Post::get_post_title());

            // Assert
            $staticMethod->wasCalledTimes(1);
        }

        public function testSomeMethodCallsSomeInstanceMethod(){
            // Setup
            $replacement = FunctionMocker::replace('Dependency::methodOne');
            FunctionMocker::replace('Dependency::methodTwo');

            // Exercise
            $caller = function(Dependency $dependency){
                $dependency->methodOne();
                $dependency->methodTwo();
            };

            $caller($replacement);

            // Assert
            $replacement->wasCalledTimes(1, 'methodOne');
            $replacement->wasCalledTimes(1, 'methodTwo');
        }
    }

## Installation
Either zip and move it to the appropriate folder or use [Composer](https://getcomposer.org/)

    composer require lucatume/function-mocker:~0.1

## Usage
In a perfect world you should never need to mock static methods and functions, should use [TDD](http://en.wikipedia.org/wiki/Test-driven_development) to write better object-oriented code and use it as a design tool.  
But sometimes a grim and sad need to mock those functions and static methods might arise and this library is here to help.

### Bootstrapping
To make Fucntion Mocker behave in its wrapping power (a power granted by [patchwork](https://github.com/antecedent/patchwork)) the `FunctionMocker::init` method needs to be called in the proper bootstrap file of [Codeception](http://codeception.com/) or [PHPUnit](http://phpunit.de/) like this

    <?php
    // This is global bootstrap for autoloading
    use tad\FunctionMocker\FunctionMocker;

    require_once dirname( __FILE__ ) . '/../vendor/autoload.php';

    FunctionMocker::init();

#### Including and excluding files from the wrapping
By default some libraries in the `vendor` folder will be excluded from the input wrapping and everything else will be included. If files in any of the `vendor` sub-folders need (or need not) to be wrapped for testing purposes, or a folder that's not in the `vendor` folder needs to be excluded, then an array of options can be passed to the `init` method like

    <?php
        // This is global bootstrap for autoloading
        use tad\FunctionMocker\FunctionMocker;

        require_once dirname( __FILE__ ) . '/../vendor/autoload.php';

        FunctionMocker::init([
            'include' => ['vendor/package', 'vendor/another'],
            'exclude' => ['libs/folder', 'src/another-folder']
        ]);

If the call to the `init` method is omitted then it will be called on the first call to the `setUp` method in the tests.

### setUp and tearDown
The library is meant to be used in the context of a [PHPUnit](http://phpunit.de/) test case and provides two `static` methods that **must** be inserted in the test case `setUp` and `tearDown` method for the function mocker to work properly:

    class MyTest extends \PHPUnit_Framework_TestCase {
        public function setUp(){
            // first
            FunctionMocker::setUp();
            ...
        }

        public function tearDown(){
            ...

            // last
            FunctionMocker::tearDown();
        }
    }

### Functions

#### Replacing functions
The library will allow for replacement of functions both defined and undefined at test run time using the `FunctionMocker::replace` method like:

    FunctionMocker::replace('myFunction', $returnValue);

and will allow setting a return value as a real value or as a function callback:
    
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

#### Spying on functions
If the value returned by the `FunctionMocker::replace` method is stored in a variable than checks for calls can be made on that function:
    
    public function testReplacedFunctionReturnsValue(){
        $myFunction = FunctionMocker::replace('myFunction', 23);

        $this->assertEquals(23, myFunction());

        $myFunction->wasCalledOnce();
        $myFunction->wasCalledWithOnce(23);
    }

The methods available for a function spy are the ones listed below in the "Methods" section.

#### Batch replacement of functions
When in need to stub out a batch of functions needed for a component to work this can be done:

    public function testBatchFunctionReplacement(){
        $functions = ['functionOne', 'functionTwo', 'functionThree', ...];

        FunctionMocker::replace($functions, function($arg){
            return $arg;
            });

        foreach ($functions as $f){
            $this->assertEquals('foo', $f('foo'));
        }
    }

When replacing a batch of functions the return value will be an `array` of spy objects that can be referenced using the function name:

    public function testBatchFunctionReplacement(){
        $functions = ['functionOne', 'functionTwo', 'functionThree', ...];

        $replacedFunctions = FunctionMocker::replace($functions, function($arg){
            return $arg;
            });
        
        functionOne();

        $functionOne = $replacedFunctions['functionOne'];
        $functionOne->wasCalledOnce();

    }

### Static methods

#### Replacing static methods
Similarly to functions the library will allow for replacement of **defined** static methods using the `FunctionMocker::replace` method

    public function testReplacedStaticMethodReturnsValue(){
        FunctionMocker::replace('Post::getContent', 'Lorem ipsum');

        $this->assertEquals('Lorem ipsum', Post::getContent());
    }

again similarly to functions a callback function return value can be set:

    public function testReplacedStaticMethodReturnsCallback(){
        FunctionMocker::replace('Post::formatTitle', function($string){
            return "foo $string baz";
            });

        $this->assertEquals('foo lorem baz', Post::formatTitle('lorem'));
    }

>Note that only `public static` methods can be replaced.

#### Spying of static methods
Storing the return value of the `FunctionMocker::replace` function allows spying on static methods using the methods listed in the "Methods" section below like:

    public function testReplacedStaticMethodReturnsValue(){
        $getContent = FunctionMocker::replace('Post::getContent', 'Lorem ipsum');

        $this->assertEquals('Lorem ipsum', Post::getContent());

        $getContent->wasCalledOnce();
        $getContent->wasNotCalledWith('some');
        ...
    }

#### Batch replacement of static methods
Static methods too can be replaced in a batch assigning to any replaced method the same return value or callback:

    public function testBatchReplaceStaticMethods(){
        $methods = ['Foo::one', 'Foo::two', 'Foo::three'];

        FunctionMocker::replace($methods, 'foo');

        $this->assertEquals('foo', Foo::one());
        $this->assertEquals('foo', Foo::two());
        $this->assertEquals('foo', Foo::three());
    }

When batch replacing static methods `FunctionMocker::replace` will return an array of spy objects indexed by the method name that can be used as any other static method spy object;

    public function testBatchReplaceStaticMethods(){
        $methods = ['Foo::one', 'Foo::two', 'Foo::three'];

        $replacedMethods = FunctionMocker::replace($methods, 'foo');
        
        Foo::one();

        $one = $replacedMethods['one'];
        $one->wasCalledOnce();
    }

### Instance methods

### Replacing instance methods
When trying to replace an instance method the `FunctionMocker::replace` method will return an extended PHPUnit mock object implementing all the [original methods](https://phpunit.de/manual/current/en/test-doubles.html) and some (see below)

    // file SomeClass.php

    class SomeClass{

        protected $dep;

        public function __construct(Dep $dep){
            $this->dep = $dep;
        }

        public function someMethod(){
            return $this->dep->go();
        }
    }

    // file SomeClassTest.php   
    
    use tad\FunctionMocker\FunctionMocker;

    class SomeClassTest extends PHPUnit_Framework_TestCase {
    
        /**
         * @test
         */
        public function it_will_call_go(){
            $dep = FunctionMocker::replace('Dep::go', 23);

            $sut = new SomeClass($dep);

            $this->assertEquals(23, $sut->someMethod());
        }
    }

The `FunctionMocker::replace` method will set up the PHPUnit mock object using the `any` method, the call above is equivalent to

    $dep->expects($this->any())->method('go')->willReturn(23);

Replacing different methods from the same class in the same test and in subsequent calls will return the same object with updated invocation expectations

#### Spying instance methods
The object returned by the `FunctionMocker::replace` method called on an instance method will allow for the methods specified in the "Methods" section to be used to check for calls made to the replaced method with the additional `methodName` parameter specified:

    // file SomeClass.php

    class SomeClass{

        public function methodOne(){
            ...
        }

        public function methodTwo(){
            ...
        }
    }

    // file SomeClassTest.php   
    
    use tad\FunctionMocker\FunctionMocker;

    class SomeClassTest extends PHPUnit_Framework_TestCase {
    
        /**
         * @test
         */
        public function returns_the_same_replacement_object(){
            // replace both class instance methods to return 23
            $replacement = FunctionMocker::replace('SomeClass::methodOne', 23);
            // $replacement === $replacement2
            $replacement2 = FunctionMocker::replace('SomeClass::methodTwo', 23);

            $replacement->methodOne();
            $replacement->methodTwo();

            $replacement->wasCalledOnce('methodOne');
            $replacement->wasCalledOnce('methodTwo');
        }
    }

#### Batch replacing instance methods
It's possible to batch replace instance method **of the same classs** using the same syntax used for batch function and static method replacement; differently from batch replacement of functions and static methods the value returned from the `FunctionMocker::replace` function can be used to spy. Given the `SomeClass` above:

        public function testBatchInstanceMethodReplacement(){
            $methods = ['SomeClass::methodOne', 'SomeClass::methodTwo'];
            // replace both class instance methods to return 23
            $replacement = FunctionMocker::replace($methods, 23);

            $replacement->methodOne();
            $replacement->methodTwo();

            $replacement->wasCalledOnce('methodOne');
            $replacement->wasCalledOnce('methodTwo');
        }

## Methods
Beside the methods defined as part of a [PHPUnit](http://phpunit.de/) mock object interface (see [here](https://phpunit.de/manual/3.7/en/test-doubles.html)), available only when replacing instance methods, the function mocker will extend the replaced functions and methods with the following methods:

* `wasCalledTimes(int $times [, string $methodName])` - will assert a PHPUnit assertion if the function or static method was called `$times` times; the `$times` parameter can come using the times syntax below.
* `wasCalledOnce([string $methodName])` - will assert a PHPUnit assertion if the function or static method was called once.
* `wasNotCalled([string $methodName])` - will assert a PHPUnit assertion if the function or static method was not called.
* `wasCalledWithTimes(array $args, int $times[, string $methodName])` - will assert a PHPUnit assertion if the function or static method was called with `$args` arguments `$times` times; the `$times` parameter can come using the times syntax below.
* `wasCalledWithOnce(array $args[, string $methodName])` - will assert a PHPUnit assertion if the function or static method was called with `$args` arguments once.
* `wasNotCalledWith(array $args[, string $methodName])` - will assert a PHPUnit assertion if the function or static method was not called with `$args` arguments.

>The method name is needed to verify calls on replaced instance methods!

### Times
When specifying the number of times a function or method should have been called a flexible syntax is available; in its most basic form can be expressed in numbers
    
    // the function should have have been called exactly 2 times
    $function->wasCalledTimes(2);

but the usage of strings makes the check less cumbersome using the comparator syntax used in PHP

    // the function should have been called at least 2 times
    $function->wasCalledTimes('>=2');

available comparators are `>n`, `<n`, `>=n`, `<=n`, `==n` (same as inserting a number), `!n`.
