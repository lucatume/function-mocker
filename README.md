#Function Mocker

*A [Patchwork](http://antecedent.github.io/patchwork/) powered function mocker.*

## Example usage
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

Documentation to come.