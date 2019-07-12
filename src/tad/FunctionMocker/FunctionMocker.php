<?php
/**
 * The main Function Mocker class.
 *
 * @package    FunctionMocker
 * @subpackage API
 * @author     Luca Tumedei <luca@theaveragedev.com>
 * @copyright  2018 Luca Tumedei
 */

namespace tad\FunctionMocker;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophecy\ProphecyInterface;
use Prophecy\Prophet;

/**
 * Class FunctionMocker
 */
class FunctionMocker
{

    /**
     * The singleton instance of the class.
     *
     * @var \tad\FunctionMocker\FunctionMocker
     */
    protected static $instance;

    /**
     * A flag value to indicate if the initialization, and potentially costly file write, did run or not.
     *
     * @var boolean
     */
    protected $didInit = false;

    /**
     * An array cache of the revealed instances, the key is the function or static method identifier.
     *
     * @var \Prophecy\Prophecy\ObjectProphecy[]
     */
    protected $revealed = [];

    /**
     * An array cache of the built object prophecies, the key is the function or static method identifier.
     *
     * @var ProphecyInterface[]
     */
    protected $prophecies = [];

    /**
     * The prophet instance used by the class.
     *
     * @var \Prophecy\Prophet
     */
    protected $prophet;

    /**
     * The current namespace used for replacement function, from Patchwork implementation.
     *
     * @var string
     */
    protected $currentReplacementNamespace;

    /**
     * @var boolean Whether checks made in the `tearDown` method should be skipped or not.
     */
    protected $skipChecks = false;

    /**
     * FunctionMocker constructor.
     *
     * @param \Prophecy\Prophet $prophet A Prophet instance that will be used to mock, stub and spy functions.
     */
    protected function __construct(Prophet $prophet)
    {
        $this->prophet = $prophet;
    }

    /**
     * Sets up the class to redefine, stub and mock functions and static methods, this method should be called  in the
     * `setUp` method of the test case and in conjunction with the `tad\FunctionMocker\FunctionMocker\tearDown` method.
     *
     * @example
     * ```php
     *  use \tad\FunctionMocker\FunctionMocker;
     *
     *  class MyTestCase extends \PHPUnit\Framework\TestCase {
     *      public function setUp(){
     *          FunctionMocker::setUp();
     *      }
     *
     *      public function test_something_requiring_function_mocking(){
     *          // ...
     *      }
     *
     *      public function tearDown(){
     *          FunctionMocker::tearDown($this);
     *      }
     *  }
     * ```
     *
     * @see \tad\FunctionMocker\FunctionMocker::tearDown()
     */
    public static function setUp()
    {
        if (!self::instance()->didInit) {
            self::init();
        }

        static::instance()->prophet = new Prophet();
    }

    /**
     * Initializes the mocking engine including the Patchwork library and configuring it.
     *
     * @param array|null $options An array of options to init the Patchwork library.
     *                               ['include'|'whitelist']     array|string A list of absolute paths that should be
     *                               included in the patching.
     *                               ['exclude'|'blacklist']     array|string A list of absolute paths that should be
     *                               excluded in the patching.
     *                               ['cache-path']              string The absolute path to the folder where Patchwork
     *                               should cache the wrapped files.
     *                               ['redefinable-internals']   array A list of internal PHP functions that are
     *                               available for replacement.
     *                               ['env']                     array|string|bool Specifies one or more environment
     *                               setup files to load immediately after including Patchwork. Set this value to an
     *                               empty array not to load any environment.
     *
     * @see \Patchwork\configure()
     */
    public static function init(array $options = null)
    {
        $function_mocker = static::instance();

        if ($function_mocker->didInit) {
            return;
        }

        $envs = readEnvsFromOptions($options);
        $options = whitelistEnvs($options, $envs);
        writePatchworkConfig($options);

        includePatchwork();

        include_once __DIR__ . '/utils.php';

        setKnownWarningsHandler();

        includeEnvs($envs);

        $function_mocker->didInit = true;
    }

    /**
     * Tears down the class artifacts after a test, use in the `tearDown` method of the test case and in conjunction
     * with the `tad\FunctionMocker\FunctionMocker\setUp` method.
     *
     * @param null|\PHPUnit\Framework\TestCase $testCase If the method is running in
     *                                                      the context of a PHPUnit `tearDown`
     *                                                      method then the the current test case should
     *                                                      be passed to this method.
     *
     * @return void
     *
     * @example
     * ```php
     *  use \tad\FunctionMocker\FunctionMocker;
     *
     *  class MyTestCase extends \PHPUnit\Framework\TestCase {
     *      public function setUp(){
     *          FunctionMocker::setUp();
     *      }
     *
     *      public function test_something_requiring_function_mocking(){
     *          // ...
     *      }
     *
     *      public function tearDown(){
     *              FunctionMocker::tearDown($this);
     *      }
     * }
     * ```
     *
     * @see \tad\FunctionMocker\FunctionMocker::setUp()
     */
    public static function tearDown($testCase = null)
    {
        \Patchwork\restoreAll();

        $instance = static::instance();

        if (!$instance->skipChecks) {
            $instance->checkPredictions($testCase);
        }

        $instance->resetProperties();
    }

    /**
     * Magic method to offer a flexible function-mocking API.
     *
     * @param string $function Handled by PHP; if calling `FunctionMocker::update_option` then this
     *                             will be `update_option`; see usage example above.
     * @param array $arguments Handled by PHP.
     *
     * @return \Prophecy\Prophecy\MethodProphecy The method prophecy as built from the `FunctionMocker::prophesize`
     *                                           method.
     * @throws \Exception If the function could not be created by the `FunctionMocker::prophesize` method.
     * @throws \tad\FunctionMocker\UsageException If the function cannot be built.
     *
     * @example
     * ```php
     *  use tad\FunctionMocker\FunctionMocker;
     *
     *  class MyTestCase extends \PHPUnit\Framework\TestCase {
     *      public function setUp(){
     *          FunctionMocker::setUp();
     *      }
     *
     *      public function test_something_requiring_function_mocking(){
     *          FunctionMocker::update_option('foo', ['bar'])
     *              ->shouldBeCalled();
     *          FunctionMocker::get_option(Argument::type('string'))
     *              ->willReturn([]);
     *
     *          $logger = new OptionLogger($option = 'foo');
     *          $logger->log('bar');
     *      }
     *
     *      public function tearDown(){
     *          FunctionMocker::tearDown($this);
     *      }
     *  }
     * ```
     *
     * @see FunctionMocker::prophesize()
     */
    public static function __callStatic($function, array $arguments)
    {
        return self::prophesize($function, ...$arguments);
    }

    /**
     * Replaces a function, be it defined or not, with Patchwork.
     *
     * This method is the one used by the `__callStatic` implementation and is the
     * one that should be used to replace namespaced functions.
     *
     * @param mixed ...$arguments Arguments that are expected to be used to call the function. The arguments
     *                                      can be scalar or instances of the `Prophecy\Argument` class.
     *
     * @param string|array $function The name of the function to replace, including the namespace, the static
     *                                      method to replace in the `Class::staticMethod` string format or the
     *                                      `[<Class>, <staticMethod>]` array format.
     *
     * @return \Prophecy\Prophecy\MethodProphecy A method prophecy for the function or static method.
     * @throws \Exception If the function could not be created.
     * @throws \tad\FunctionMocker\UsageException If trying to spy an invalid class and static method combination.
     *
     * @example
     * ```php
     *  use tad\FunctionMocker\FunctionMocker;
     *
     *  class MyTestCase extends \PHPUnit\Framework\TestCase {
     *      public function setUp(){
     *          FunctionMocker::setUp();
     *      }
     *
     *      public function test_something_requiring_function_mocking(){
     *          FunctionMocker::prophesize('MyNamespace\\SubSpace\\update_option', 'foo', ['bar'])
     *              ->shouldBeCalled();
     *          FunctionMocker::prophesize('MyNamespace\\SubSpace\\get_option', Argument::type('string'))
     *              ->willReturn([]);
     *
     *          $logger = new OptionLogger($option = 'foo');
     *          $logger->log('bar');
     *      }
     *
     *      public function tearDown(){
     *          FunctionMocker::tearDown($this);
     *      }
     *  }
     * ```
     *
     * @see \Prophecy\Argument
     */
    public static function prophesize($function, ...$arguments)
    {
        $instance = self::instance();

        if (is_array($function)) {
            if (count($function) !== 2) {
                throw UsageException::becauseArrayDoesNotDefineAClassAndMethodCallable($function);
            }
            if (!is_callable($function)) {
                throw UsageException::becauseClassAndMethodCoupleIsNotCallable($function[0], $function[1]);
            }

            // Transform the class and static method callable into its string form.
            $function = $function[0] . '::' . $function[1];
        }

        list($function, $namespace, $functionFQN) = extractFunctionAndNamespace($function);

        if ($instance->currentReplacementNamespace !== null) {
            $namespace = rtrim(
                '\\' . $instance->currentReplacementNamespace . '\\' . ltrim($namespace, '\\'),
                '\\'
            );
            $functionFQN = $namespace . '\\' . trim($function, '\\');
        }

        if (!(
            function_exists($functionFQN)
            || is_callable("{$functionFQN}::{$function}")
        )) {
            \tad\FunctionMocker\createFunction($function, $namespace);
        }

        if ($instance->buildNewProphecyFor($function, $functionFQN)) {
            $instance->redefineFunctionWithPatchwork($function, $functionFQN);
        }

        $redefineTarget = $instance->getRedefineTarget($function, $functionFQN);

        return $instance->buildMethodProphecy($function, $arguments, $instance->prophecies[$redefineTarget]);
    }

    /**
     * Localizes all function redefinitions in a namespace.
     *
     * @param string $namespace The namespace the redefinitions should happen into.
     * @param callable $redefinitions A closure detailing the redefinitions that should happen
     *                                   in the namespace.
     * @example
     * ```php
     *  use \tad\FunctionMocker\FunctionMocker;
     *
     *  class MyTestCase extends \PHPUnit\Framework\TestCase {
     *      public function setUp(){
     *          FunctionMocker::setUp();
     *      }
     *
     *      public function test_something_requiring_function_mocking(){
     *          FunctionMocker::inNamespace('Acme', function(){
     *              FunctionMocker::aFunction('foo')->willReturn('bar');
     *              FunctionMocker::anotherFunction('bar')->shouldBeCalled();
     *          });
     *
     *          $this->assertEquals('bar', \Acme\aFunction('foo'));
     *          \Acme\anotherFunction('bar');
     *      }
     *
     *      public function tearDown(){
     *          FunctionMocker::tearDown($this);
     *      }
     *  }
     * ```
     *
     */
    public static function inNamespace($namespace, callable $redefinitions)
    {
        self::instance()->currentReplacementNamespace = trim($namespace, '\\');

        $redefinitions();

        self::instance()->currentReplacementNamespace = null;
    }

    /**
     * Starts spying a function for its calls.
     *
     * @param string|array $function The name of the function to replace, including the namespace, the static
     *                                      method to replace in the `Class::staticMethod` string format or the
     *                                      `[<Class>, <staticMethod>]` array format.
     *
     * @return \Prophecy\Prophecy\MethodProphecy The method prophecy built to spy on the function or static method.
     * @throws \Exception If the function could not be created.*@throws \tad\FunctionMocker\UsageException
     * @throws \tad\FunctionMocker\UsageException If trying to spy an invalid class and static method combination.
     * @see FunctionMocker::prophesize()
     */
    public static function spy($function)
    {
        return static::prophesize($function);
    }

    /**
     * Avoids checks on predictions from being performed at `tearDown` time
     */
    public static function skipChecks()
    {
        static::instance()->skipChecks = true;
    }

    /**
     * Replaces a function or static method returning to make it return a specified value.
     *
     * This is a shortcut to use the `FunctionMocker::prophesize` method and call `willReturn($value)` on it.
     *
     * @param string $functionOrStaticMethod The fully qualified name of an existing function, the fully
     *                                              qualified name of a non-existing function, the fully qualified
     *                                              string representing a `Class::staticMethod` or an array in the
     *                                              shape
     *                                              `[ <Class>, <staticMethod> ]`.
     * @param null|mixed $value The value the function or static method will return when called.
     *
     * @return \Prophecy\Prophecy\MethodProphecy The method prophecy built for the function or static method.
     * @throws \Exception If the function does not exist and it could not be created.
     * @throws \tad\FunctionMocker\UsageException If trying to spy an invalid class and static method combination.
     * @example
     * ```php
     *  use tad\FunctionMocker\FunctionMocker;
     *
     *  class MyTestCase extends \PHPUnit\Framework\TestCase {
     *      public function setUp(){
     *          FunctionMocker::setUp();
     *      }
     *
     *      public function test_something_requiring_function_mocking(){
     *          FunctionMocker::prophesize('MyNamespace\\SubSpace\\update_option', 'foo', ['bar'])
     *              ->shouldBeCalled();
     *          FunctionMocker::replace('MyNamespace\\SubSpace\\get_option', []);
     *
     *          $logger = new OptionLogger($option = 'foo');
     *          $logger->log('bar');
     *      }
     *
     *      public function tearDown(){
     *          FunctionMocker::tearDown($this);
     *      }
     *  }
     * ```
     *
     */
    public static function replace($functionOrStaticMethod, $value = null)
    {
        if (!is_callable($value)) {
            return static::prophesize($functionOrStaticMethod, Argument::any())->willReturn($value);
        }

        // Use a proxy function to unpack the call arguments.
        $proxy = function ($prophecyArgs) use ($value) {
            return $value(...$prophecyArgs);
        };

        return static::prophesize($functionOrStaticMethod, Argument::cetera())->will($proxy);
    }

    /**
     * An alias of the `prophesize` method to allow writing more eloquent testing functions.
     *
     * @param string|array $classAndStaticMethod An array of class and static method in the shape
     *                                              `[ <Class>, <staticMethod> ]` or a string in the format
     *                                              `<Class>::<staticMethod>`.
     *
     * @return MethodProphecy The method prophecy on the static method call.
     * @throws UsageException If the class and static method is not valid.
     */
    public static function ofClass($classAndStaticMethod)
    {
        return static::prophesize($classAndStaticMethod);
    }

    /**
     * Singleton accessor for the class.
     *
     * @return \tad\FunctionMocker\FunctionMocker
     */
    protected static function instance()
    {
        if (static::$instance === null) {
            static::$instance = new static(new Prophet());
        }

        return static::$instance;
    }

    /**
     * Checks the current predictions.
     *
     * This method should run at the end of test case; it will be run by the `FunctionMocker::tearDown` method.
     *
     * @param TestCase|\PHPUnit_Framework_TestCase $testCase A PHPUnit-like testcase instance; if running in the
     *                                                          context of PHPUnit-like tests then predictions will be
     *                                                          added to the test case expectations.
     *
     * @throws \Prophecy\Exception\Prediction\AggregateException If any prediction fails.
     */
    protected function checkPredictions($testCase = null)
    {
        if ($this->prophet === null) {
            return;
        }

        $prophet = $this->prophet;
        $this->prophet = null;

        if (class_exists('\PHPUnit\Framework\TestCase') && $testCase instanceof \PHPUnit\Framework\TestCase) {
            $testCase->addToAssertionCount(count($prophet->getProphecies()));
        } elseif (class_exists('PHPUnit_Framework_TestCase') && $testCase instanceof PHPUnit_Framework_TestCase) {
            $testCase->addToAssertionCount(count($prophet->getProphecies()));
        } else {
            $prophet->checkPredictions();
        }
    }

    /**
     * Resets the object properties to their initial state.
     *
     * This method will be run from the `FunctionMocker::tearDown` method.
     */
    protected function resetProperties()
    {
        unset($this->prophet);
        $this->prophecies = [];
        $this->revealed = [];
    }

    /**
     * Builds a prophecy for a function, or static method, or returns an already built one.
     *
     * @param string $function The function or static method name.
     * @param string $functionFQN The function fully-qualified name.
     *
     * @return boolean|\Prophecy\Prophecy\ObjectProphecy
     */
    protected function buildNewProphecyFor($function, $functionFQN)
    {
        $redefineTarget = $this->getRedefineTarget($function, $functionFQN);

        if (array_key_exists($redefineTarget, $this->prophecies)) {
            return false;
        }

        $class = $this->createClassForFunction($function, $functionFQN);
        $prophecy = $this->prophet->prophesize($class);
        $this->prophecies[$redefineTarget] = $prophecy;

        return $prophecy;
    }

    /**
     * Depending on the function name and fully-qualified name returns the redefine target that should be used.
     *
     * @param string $function The name of the function w/o namespace, or the statc method name.
     * @param string $functionFQN The fully-qualified function name or the fully-qualified class static method class
     *                               name.
     * @param string $separator The separator to use between the class and the static method name; defaults to `_`.
     *
     * @return string The name that will be used across the request to identify the function/method/class combination.
     */
    protected function getRedefineTarget($function, $functionFQN, $separator = '_')
    {
        $redefineTarget = is_callable("{$functionFQN}::{$function}") ?
            $functionFQN . $separator . $function
            : $functionFQN;

        return $redefineTarget;
    }

    /**
     * Creates a class to be prophesized for the function.
     *
     * This method is based on `eval` to be PHP 5.6 compatible. The created class
     * will be one defining only one public method named like the function.
     *
     * @param string $function The function name.
     * @param string $functionFQN The function fully-qualified name.
     *
     * @return string The name of the class created for the function or static method.
     */
    protected function createClassForFunction($function, $functionFQN)
    {
        $uniqid = md5(uniqid('function-', true));
        $functionSlug = str_replace('\\', '_', $functionFQN);

        $className = "_{$functionSlug}_{$uniqid}";

        //phpcs:ignore
        eval("class {$className}{ public function {$function}(){}}");

        return $className;
    }

    /**
     * Invokes the Patchwork `redefine` function and attaches a prophecy to it.
     *
     * @param string $function The function name.
     * @param string $functionFQN The function fully-qualified name.
     */
    protected function redefineFunctionWithPatchwork($function, $functionFQN)
    {
        $redefineTarget = $this->getRedefineTarget($function, $functionFQN);
        \Patchwork\redefine(
            $this->getRedefineTarget($function, $functionFQN, '::'),
            function () use ($redefineTarget, $function) {
                $prophecy = FunctionMocker::instance()->getRevealedProphecyFor($redefineTarget);
                $args = func_get_args();

                if (empty($args)) {
                    return $prophecy->$function();
                }

                return call_user_func_array([$prophecy, $function], $args);
            }
        );
    }

    /**
     * Returns a revealed function prophecy, or reveals and returns it if required.
     *
     * @param string $function The function fully-qualified name.
     *
     * @return object The object resulting from the prophecy revelation.
     */
    protected function getRevealedProphecyFor($function)
    {
        if (!array_key_exists($function, $this->revealed)) {
            $this->revealed[$function] = $this->prophecies[$function]->reveal();
        }

        return $this->revealed[$function];
    }

    /**
     * Builds a method prophecy for the function or static method..
     *
     * The prophecy is built on the public method of the prophesized class created for the function.
     *
     * @param string $function The function fully-qualified name.
     * @param array $arguments The function call arguments.
     * @param \Prophecy\Prophecy\ProphecyInterface $prophecy The class prophecy built on the class created
     *                                                           for the function.
     *
     * @return MethodProphecy The built method prophecy for the function or static method.
     * @throws \ReflectionException If the method prophecy "hacking" to clean up the prophecy method prophecies fails.
     */
    protected function buildMethodProphecy($function, array $arguments, ProphecyInterface $prophecy)
    {
        $this->removePreviousMethodProfecies($prophecy, $function);

        if (empty($arguments)) {
            $methodProphecy = $prophecy->$function();
        } else {
            $methodProphecy = call_user_func_array([$prophecy, $function], $arguments);
        }

        return $methodProphecy;
    }

    /**
     * Removes existing method prophecies from the prophecy to avoid following method redefinitions not having effect.
     *
     * This is really an hack to choose between one of two evils: either build a different prophecy for each function
     * and/or class method combination (with the included `eval` run) or do this. This might change in the future as
     * meddling with a private property is really not ideal.
     *
     * @param \Prophecy\Prophecy\ObjectProphecy $prophecy The Prophecy object to remove the method prophecies
     *                                                          for the function, or static method, from.
     * @param string $function The function, or static method name, to remove the
     *                                                          existing prophecies for.
     *
     * @throws \ReflectionException If the private `$methodProphecies` property on the object prophecy cannot be
     *                              accessed.
     */
    protected function removePreviousMethodProfecies(ObjectProphecy $prophecy, $function)
    {
        $methodProphecies = $prophecy->getMethodProphecies();

        if (!empty($methodProphecies)) {
            $prophecyMethodProphecies = new \ReflectionProperty(ObjectProphecy::class, 'methodProphecies');
            $prophecyMethodProphecies->setAccessible(true);
            $methodProphecies[$function] = [];
            $prophecyMethodProphecies->setValue($prophecy, $methodProphecies);
        }
    }
}
