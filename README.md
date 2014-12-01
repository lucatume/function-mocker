#Function Mocker

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

    // file SomeClass.php

    class SomeClass {

        protected $postContent;

        public function __construct($postId) {
            // static method
            $this->postContent = Post::getPostContent($postId);

            if($this->postContent) {
                // function
                $this->postContent = manipulatePostContent($this->postContent);
            }
        }
    }

as badly as it's written it can be tested in a [PHPUnit](http://phpunit.de/) test case like

    // file SomeClassTest.php   

    use tad\FunctionMocker\FunctionMocker;

    class SomeClassTest extends PHPUnit_Framework_TestCase {
    
        /**
         * @test
         */
        public function it_will_call_manipulatePostContent(){
            FunctionMocker::replace('Post::getPostContent', 'foo');
            $f = FunctionMocker::replace('manipulatePostContent');

            new SomeClass(23);

            $f->wasCalledWithTimes(['foo'], 1);
        }

    }

When trying to replace an instance method the `FunctionMocker::replace` will return an extended PHPUnit mock object implementing all the [original methods](https://phpunit.de/manual/current/en/test-doubles.html) and some (see below)

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
            FunctionMocker::replace('SomeClass::methodTwo', 23);

            $replacement->methodOne();
            $replacement->methodTwo();

            // passes
            $replacement->wasCalledOnce('methodOne');
            // passes
            $replacement->wasCalledOnce('methodTwo');
        }
    }

## Methods
Beside the methods defined as part of a [PHPUnit](http://phpunit.de/) mock object interface (see [here](https://phpunit.de/manual/3.7/en/test-doubles.html)), available on a replaced instance methods only, the function mocker will extend the replaced functions and methods with the following methods:

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
