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

    use tad\FunctionMocker\FunctionMocker ;

    class SomeClassTest extends \PHPUnit_Framework_TestCase {

        public function testSomeMethodCallsSomeFunction(){
            // Setup
            $functionMock = FunctionMocker::replace('some_function');

            // Exercise
            some_function();

            // Assert
            $functionMock->wasCalledTimes(1);
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
            FunctionMocker::replace('Post::getPostContent', 'foo');
            $f = FunctionMocker::replace('manipulatePostContent');

            new SomeClass(23);

            $f->wasCalledWithTimes(['foo'], 1);
        }

    }

When trying to replace an instance method the `FunctionMocker::replace` will return an extended PHPUnit mock object implementing all the [original methods](https://phpunit.de/manual/current/en/test-doubles.html).

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
        public function it_will_call_manipulatePostContent(){
            $dep = FunctionMocker::replace('Dep::go', 23);

            $sut = new SomeClass($dep);

            $this->assertEquals(23, $sut->someMethod());
        }
    }

The `FunctionMocker::mock` method will set up the PHPUnit mock object using the `any` method, the call above is equivalent to

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
            $methodOne = FunctionMocker::replace('SomeClass::methodOne', 23);
            $methodTwo = FunctionMocker::replace('SomeClass::methodTwo', 23);
            
            // passes
            $this->assertSame($methodOne, $methodOne);

            $methodOne->methodOne();
            // same object so no difference which one is called
            $methodOne->methodTwo();

            // passes
            $methodOne->wasCalledOnce();
            // passes
            $methodTwo->wasCalledOnce();
        }
    }

## Methods
Beside the methods defined as part of a [PHPUnit](http://phpunit.de/) mock object interface (see [here](https://phpunit.de/manual/3.7/en/test-doubles.html)) the function mocker will extend the replaced objects with the following methods:

* `wasCalledTimes(int $times)` - will assert a PHPUnit assertion if the function or static method was called `$times` times.
* `wasCalledOnce()` - will assert a PHPUnit assertion if the function or static method was called once.
* `wasNotCalled()` - will assert a PHPUnit assertion if the function or static method was not called.
* `wasCalledWithTimes(array $args, int $times)` - will assert a PHPUnit assertion if the function or static method was called with `$args` arguments `$times` times.
* `wasCalledWithOnce(array $args)` - will assert a PHPUnit assertion if the function or static method was called with `$args` arguments once.
* `wasNotCalledWith(array $args)` - will assert a PHPUnit assertion if the function or static method was not called with `$args` arguments.
