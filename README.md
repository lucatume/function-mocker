#Function Mocker

*A [Patchwork](http://antecedent.github.io/patchwork/) powered function mocker.*

## Show me the code
Given this code

    class SomeClass {
        public function someMethod(){
            some_function();
        }
    }

This can be written in a [PHPUnit](http://phpunit.de/) test suite

    use tad\FunctionMocker\FunctionMocker as FMocker;

    class SomeClassTest extends \PHPUnit_Framework_TestCase {

        public function testSomeMethodCallsSomeFunction(){
            // Setup
            // mocking a defined function
            $functionMock = FMocker::mock('some_function');
            // mocking an undefined function
            $undefinedFunctionMock = FMocker::mock('undefined_function');
            $sut = new SomeClass();

            // Exercise
            $sut->someMethod();

            // Assert
            $functionMock->wasCalledTimes(1);
            $undefinedFunctionMock->wasNotCalled();
        }
    }

## Installation
Either zip and move it to the appropriate folder or use [Composer](https://getcomposer.org/)

    composer require lucatume/function-mocker:~0.1

## Usage
In a perfect world you should never need to mock static methods and functions, should use [TDD](http://en.wikipedia.org/wiki/Test-driven_development) to write better object-oriented code and using it as a design tool.  
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
            FunctionMocker::mock('Post::getPostContent', 'foo');
            $f = FunctionMocker::mock('manipulatePostContent');

            new SomeClass(23);

            $f->wasCalledWithTimes(['foo'], 1);
        }

    }

When trying to mock instance method the `FunctionMocker::mock` will merely return a PHPUnit mock object acting, for all intents and purposes, as such

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
            $dep = FunctionMocker::mock('Dep::go', 23);

            $sut = new SomeClass($dep);

            $this->assertEquals(23, $sut->someMethod());
        }
    }

The `FunctionMocker::mock` method will set up the PHPUnit mock object using the `any` method, the call above is equivalent to

    $dep->expects($this->any())->method('go')->willReturn(23);

## Methods
For methods related to checks and expectations for instance methods refer to [PHPUnit](http://phpunit.de/), these metods will apply to any function and static method mocked

* `wasCalledTimes(int $times)` - will assert a PHPUnit assertion if the function or static method was called `$times` times.
* `wasCalledWithTimes(array $args, int $times)` - will assert a PHPUnit assertion if the function or static method was called with `$args` arguments `$times` times.
* `wasNotCalledTimes(int $times)` - will assert a PHPUnit assertion if the function or static method was not called `$times` times.
* `wasNotCalledWithTimes(array $args, int $times)` - will assert a PHPUnit assertion if the function or static method was not called with `$args` arguments `$times` times.
