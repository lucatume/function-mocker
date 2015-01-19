<?php

namespace tad\FunctionMocker;


trait PHPUnitFrameworkAssertWrapper
{
    /**
     * Asserts that an array has a specified key.
     *
     * @param mixed $key
     * @param array|ArrayAccess $array
     * @param string $message
     * @since Method available since Release 3.0.0
     */
    public static function assertArrayHasKey($key, $array, $message = '')
    {
        self::$testCase->assertArrayHasKey($key, $array, $message);
    }

    /**
     * Asserts that an array has a specified subset.
     *
     * @param array|ArrayAccess $subset
     * @param array|ArrayAccess $array
     * @param boolean $strict Check for object identity
     * @param string $message
     * @since Method available since Release 4.4.0
     */
    public static function assertArraySubset($subset, $array, $strict = false, $message = '')
    {
        self::$testCase->assertArraySubset($subset, $array, $strict, $message);
    }

    /**
     * Asserts that an array does not have a specified key.
     *
     * @param mixed $key
     * @param array|ArrayAccess $array
     * @param string $message
     * @since  Method available since Release 3.0.0
     */
    public static function assertArrayNotHasKey($key, $array, $message = '')
    {
        self::$testCase->assertArrayNotHasKey($key, $array, $message);
    }

    /**
     * Asserts that a haystack contains a needle.
     *
     * @param mixed $needle
     * @param mixed $haystack
     * @param string $message
     * @param boolean $ignoreCase
     * @param boolean $checkForObjectIdentity
     * @param boolean $checkForNonObjectIdentity
     * @since  Method available since Release 2.1.0
     */
    public static function assertContains($needle, $haystack, $message = '', $ignoreCase = false, $checkForObjectIdentity = true, $checkForNonObjectIdentity = false)
    {
        self::$testCase->assertContains($needle, $haystack, $message, $ignoreCase, $checkForObjectIdentity, $checkForNonObjectIdentity);
    }

    /**
     * Asserts that a haystack that is stored in a static attribute of a class
     * or an attribute of an object contains a needle.
     *
     * @param mixed $needle
     * @param string $haystackAttributeName
     * @param mixed $haystackClassOrObject
     * @param string $message
     * @param boolean $ignoreCase
     * @param boolean $checkForObjectIdentity
     * @param boolean $checkForNonObjectIdentity
     * @since  Method available since Release 3.0.0
     */
    public static function assertAttributeContains($needle, $haystackAttributeName, $haystackClassOrObject, $message = '', $ignoreCase = false, $checkForObjectIdentity = true, $checkForNonObjectIdentity = false)
    {
        self::$testCase->assertAttributeContains($needle, $haystackAttributeName, $haystackClassOrObject, $message, $ignoreCase, $checkForObjectIdentity, $checkForNonObjectIdentity);
    }

    /**
     * Asserts that a haystack does not contain a needle.
     *
     * @param mixed $needle
     * @param mixed $haystack
     * @param string $message
     * @param boolean $ignoreCase
     * @param boolean $checkForObjectIdentity
     * @param boolean $checkForNonObjectIdentity
     * @since  Method available since Release 2.1.0
     */
    public static function assertNotContains($needle, $haystack, $message = '', $ignoreCase = false, $checkForObjectIdentity = true, $checkForNonObjectIdentity = false)
    {
        self::$testCase->assertNotContains($needle, $haystack, $message, $ignoreCase, $checkForObjectIdentity, $checkForNonObjectIdentity);
    }

    /**
     * Asserts that a haystack that is stored in a static attribute of a class
     * or an attribute of an object does not contain a needle.
     *
     * @param mixed $needle
     * @param string $haystackAttributeName
     * @param mixed $haystackClassOrObject
     * @param string $message
     * @param boolean $ignoreCase
     * @param boolean $checkForObjectIdentity
     * @param boolean $checkForNonObjectIdentity
     * @since  Method available since Release 3.0.0
     */
    public static function assertAttributeNotContains($needle, $haystackAttributeName, $haystackClassOrObject, $message = '', $ignoreCase = false, $checkForObjectIdentity = true, $checkForNonObjectIdentity = false)
    {
        self::$testCase->assertAttributeNotContains($needle, $haystackAttributeName, $haystackClassOrObject, $message, $ignoreCase, $checkForObjectIdentity, $checkForNonObjectIdentity);
    }

    /**
     * Asserts that a haystack contains only values of a given type.
     *
     * @param string $type
     * @param mixed $haystack
     * @param boolean $isNativeType
     * @param string $message
     * @since  Method available since Release 3.1.4
     */
    public static function assertContainsOnly($type, $haystack, $isNativeType = null, $message = '')
    {
        self::$testCase->assertContainsOnly($type, $haystack, $isNativeType, $message);
    }

    /**
     * Asserts that a haystack contains only instances of a given classname
     *
     * @param string $classname
     * @param array|Traversable $haystack
     * @param string $message
     */
    public static function assertContainsOnlyInstancesOf($classname, $haystack, $message = '')
    {
        self::$testCase->assertContainsOnlyInstancesOf($classname, $haystack, $message);
    }

    /**
     * Asserts that a haystack that is stored in a static attribute of a class
     * or an attribute of an object contains only values of a given type.
     *
     * @param string $type
     * @param string $haystackAttributeName
     * @param mixed $haystackClassOrObject
     * @param boolean $isNativeType
     * @param string $message
     * @since  Method available since Release 3.1.4
     */
    public static function assertAttributeContainsOnly($type, $haystackAttributeName, $haystackClassOrObject, $isNativeType = null, $message = '')
    {
        self::$testCase->assertAttributeContainsOnly($type, $haystackAttributeName, $haystackClassOrObject, $isNativeType, $message);
    }

    /**
     * Asserts that a haystack does not contain only values of a given type.
     *
     * @param string $type
     * @param mixed $haystack
     * @param boolean $isNativeType
     * @param string $message
     * @since  Method available since Release 3.1.4
     */
    public static function assertNotContainsOnly($type, $haystack, $isNativeType = null, $message = '')
    {
        self::$testCase->assertNotContainsOnly($type, $haystack, $isNativeType, $message);
    }

    /**
     * Asserts that a haystack that is stored in a static attribute of a class
     * or an attribute of an object does not contain only values of a given
     * type.
     *
     * @param string $type
     * @param string $haystackAttributeName
     * @param mixed $haystackClassOrObject
     * @param boolean $isNativeType
     * @param string $message
     * @since  Method available since Release 3.1.4
     */
    public static function assertAttributeNotContainsOnly($type, $haystackAttributeName, $haystackClassOrObject, $isNativeType = null, $message = '')
    {
        self::$testCase->assertAttributeNotContainsOnly($type, $haystackAttributeName, $haystackClassOrObject, $isNativeType, $message);
    }

    /**
     * Asserts the number of elements of an array, Countable or Traversable.
     *
     * @param integer $expectedCount
     * @param mixed $haystack
     * @param string $message
     */
    public static function assertCount($expectedCount, $haystack, $message = '')
    {
        self::$testCase->assertCount($expectedCount, $haystack, $message);
    }

    /**
     * Asserts the number of elements of an array, Countable or Traversable
     * that is stored in an attribute.
     *
     * @param integer $expectedCount
     * @param string $haystackAttributeName
     * @param mixed $haystackClassOrObject
     * @param string $message
     * @since Method available since Release 3.6.0
     */
    public static function assertAttributeCount($expectedCount, $haystackAttributeName, $haystackClassOrObject, $message = '')
    {
        self::$testCase->assertAttributeCount($expectedCount, $haystackAttributeName, $haystackClassOrObject, $message);
    }

    /**
     * Asserts the number of elements of an array, Countable or Traversable.
     *
     * @param integer $expectedCount
     * @param mixed $haystack
     * @param string $message
     */
    public static function assertNotCount($expectedCount, $haystack, $message = '')
    {
        self::$testCase->assertNotCount($expectedCount, $haystack, $message);
    }

    /**
     * Asserts the number of elements of an array, Countable or Traversable
     * that is stored in an attribute.
     *
     * @param integer $expectedCount
     * @param string $haystackAttributeName
     * @param mixed $haystackClassOrObject
     * @param string $message
     * @since Method available since Release 3.6.0
     */
    public static function assertAttributeNotCount($expectedCount, $haystackAttributeName, $haystackClassOrObject, $message = '')
    {
        self::$testCase->assertAttributeNotCount($expectedCount, $haystackAttributeName, $haystackClassOrObject, $message);
    }

    /**
     * Asserts that two variables are equal.
     *
     * @param mixed $expected
     * @param mixed $actual
     * @param string $message
     * @param float $delta
     * @param integer $maxDepth
     * @param boolean $canonicalize
     * @param boolean $ignoreCase
     */
    public static function assertEquals($expected, $actual, $message = '', $delta = 0.0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false)
    {
        self::$testCase->assertEquals($expected, $actual, $message, $delta, $maxDepth, $canonicalize, $ignoreCase);
    }

    /**
     * Asserts that a variable is equal to an attribute of an object.
     *
     * @param mixed $expected
     * @param string $actualAttributeName
     * @param string $actualClassOrObject
     * @param string $message
     * @param float $delta
     * @param integer $maxDepth
     * @param boolean $canonicalize
     * @param boolean $ignoreCase
     */
    public static function assertAttributeEquals($expected, $actualAttributeName, $actualClassOrObject, $message = '', $delta = 0.0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false)
    {
        self::$testCase->assertAttributeEquals($expected, $actualAttributeName, $actualClassOrObject, $message, $delta, $maxDepth, $canonicalize, $ignoreCase);
    }

    /**
     * Asserts that two variables are not equal.
     *
     * @param mixed $expected
     * @param mixed $actual
     * @param string $message
     * @param float $delta
     * @param integer $maxDepth
     * @param boolean $canonicalize
     * @param boolean $ignoreCase
     * @since  Method available since Release 2.3.0
     */
    public static function assertNotEquals($expected, $actual, $message = '', $delta = 0.0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false)
    {
        self::$testCase->assertNotEquals($expected, $actual, $message, $delta, $maxDepth, $canonicalize, $ignoreCase);
    }

    /**
     * Asserts that a variable is not equal to an attribute of an object.
     *
     * @param mixed $expected
     * @param string $actualAttributeName
     * @param string $actualClassOrObject
     * @param string $message
     * @param float $delta
     * @param integer $maxDepth
     * @param boolean $canonicalize
     * @param boolean $ignoreCase
     */
    public static function assertAttributeNotEquals($expected, $actualAttributeName, $actualClassOrObject, $message = '', $delta = 0.0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false)
    {
        self::$testCase->assertAttributeNotEquals($expected, $actualAttributeName, $actualClassOrObject, $message, $delta, $maxDepth, $canonicalize, $ignoreCase);
    }

    /**
     * Asserts that a variable is empty.
     *
     * @param  mixed $actual
     * @param  string $message
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    public static function assertEmpty($actual, $message = '')
    {
        self::$testCase->assertEmpty($actual, $message);
    }

    /**
     * Asserts that a static attribute of a class or an attribute of an object
     * is empty.
     *
     * @param string $haystackAttributeName
     * @param mixed $haystackClassOrObject
     * @param string $message
     * @since Method available since Release 3.5.0
     */
    public static function assertAttributeEmpty($haystackAttributeName, $haystackClassOrObject, $message = '')
    {
        self::$testCase->assertAttributeEmpty($haystackAttributeName, $haystackClassOrObject, $message);
    }

    /**
     * Asserts that a variable is not empty.
     *
     * @param  mixed $actual
     * @param  string $message
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    public static function assertNotEmpty($actual, $message = '')
    {
        self::$testCase->assertNotEmpty($actual, $message);
    }

    /**
     * Asserts that a static attribute of a class or an attribute of an object
     * is not empty.
     *
     * @param string $haystackAttributeName
     * @param mixed $haystackClassOrObject
     * @param string $message
     * @since Method available since Release 3.5.0
     */
    public static function assertAttributeNotEmpty($haystackAttributeName, $haystackClassOrObject, $message = '')
    {
        self::$testCase->assertAttributeNotEmpty($haystackAttributeName, $haystackClassOrObject, $message);
    }

    /**
     * Asserts that a value is greater than another value.
     *
     * @param mixed $expected
     * @param mixed $actual
     * @param string $message
     * @since  Method available since Release 3.1.0
     */
    public static function assertGreaterThan($expected, $actual, $message = '')
    {
        self::$testCase->assertGreaterThan($expected, $actual, $message);
    }

    /**
     * Asserts that an attribute is greater than another value.
     *
     * @param mixed $expected
     * @param string $actualAttributeName
     * @param string $actualClassOrObject
     * @param string $message
     * @since  Method available since Release 3.1.0
     */
    public static function assertAttributeGreaterThan($expected, $actualAttributeName, $actualClassOrObject, $message = '')
    {
        self::$testCase->assertAttributeGreaterThan($expected, $actualAttributeName, $actualClassOrObject, $message);
    }

    /**
     * Asserts that a value is greater than or equal to another value.
     *
     * @param mixed $expected
     * @param mixed $actual
     * @param string $message
     * @since  Method available since Release 3.1.0
     */
    public static function assertGreaterThanOrEqual($expected, $actual, $message = '')
    {
        self::$testCase->assertGreaterThanOrEqual($expected, $actual, $message);
    }

    /**
     * Asserts that an attribute is greater than or equal to another value.
     *
     * @param mixed $expected
     * @param string $actualAttributeName
     * @param string $actualClassOrObject
     * @param string $message
     * @since  Method available since Release 3.1.0
     */
    public static function assertAttributeGreaterThanOrEqual($expected, $actualAttributeName, $actualClassOrObject, $message = '')
    {
        self::$testCase->assertAttributeGreaterThanOrEqual($expected, $actualAttributeName, $actualClassOrObject, $message);
    }

    /**
     * Asserts that a value is smaller than another value.
     *
     * @param mixed $expected
     * @param mixed $actual
     * @param string $message
     * @since  Method available since Release 3.1.0
     */
    public static function assertLessThan($expected, $actual, $message = '')
    {
        self::$testCase->assertLessThan($expected, $actual, $message);
    }

    /**
     * Asserts that an attribute is smaller than another value.
     *
     * @param mixed $expected
     * @param string $actualAttributeName
     * @param string $actualClassOrObject
     * @param string $message
     * @since  Method available since Release 3.1.0
     */
    public static function assertAttributeLessThan($expected, $actualAttributeName, $actualClassOrObject, $message = '')
    {
        self::$testCase->assertAttributeLessThan($expected, $actualAttributeName, $actualClassOrObject, $message);
    }

    /**
     * Asserts that a value is smaller than or equal to another value.
     *
     * @param mixed $expected
     * @param mixed $actual
     * @param string $message
     * @since  Method available since Release 3.1.0
     */
    public static function assertLessThanOrEqual($expected, $actual, $message = '')
    {
        self::$testCase->assertLessThanOrEqual($expected, $actual, $message);
    }

    /**
     * Asserts that an attribute is smaller than or equal to another value.
     *
     * @param mixed $expected
     * @param string $actualAttributeName
     * @param string $actualClassOrObject
     * @param string $message
     * @since  Method available since Release 3.1.0
     */
    public static function assertAttributeLessThanOrEqual($expected, $actualAttributeName, $actualClassOrObject, $message = '')
    {
        self::$testCase->assertAttributeLessThanOrEqual($expected, $actualAttributeName, $actualClassOrObject, $message);
    }

    /**
     * Asserts that the contents of one file is equal to the contents of another
     * file.
     *
     * @param string $expected
     * @param string $actual
     * @param string $message
     * @param boolean $canonicalize
     * @param boolean $ignoreCase
     * @since  Method available since Release 3.2.14
     */
    public static function assertFileEquals($expected, $actual, $message = '', $canonicalize = false, $ignoreCase = false)
    {
        self::$testCase->assertFileEquals($expected, $actual, $message, $canonicalize, $ignoreCase);
    }

    /**
     * Asserts that the contents of one file is not equal to the contents of
     * another file.
     *
     * @param string $expected
     * @param string $actual
     * @param string $message
     * @param boolean $canonicalize
     * @param boolean $ignoreCase
     * @since  Method available since Release 3.2.14
     */
    public static function assertFileNotEquals($expected, $actual, $message = '', $canonicalize = false, $ignoreCase = false)
    {
        self::$testCase->assertFileNotEquals($expected, $actual, $message, $canonicalize, $ignoreCase);
    }

    /**
     * Asserts that the contents of a string is equal
     * to the contents of a file.
     *
     * @param string $expectedFile
     * @param string $actualString
     * @param string $message
     * @param boolean $canonicalize
     * @param boolean $ignoreCase
     * @since  Method available since Release 3.3.0
     */
    public static function assertStringEqualsFile($expectedFile, $actualString, $message = '', $canonicalize = false, $ignoreCase = false)
    {
        self::$testCase->assertStringEqualsFile($expectedFile, $actualString, $message, $canonicalize, $ignoreCase);
    }

    /**
     * Asserts that the contents of a string is not equal
     * to the contents of a file.
     *
     * @param string $expectedFile
     * @param string $actualString
     * @param string $message
     * @param boolean $canonicalize
     * @param boolean $ignoreCase
     * @since  Method available since Release 3.3.0
     */
    public static function assertStringNotEqualsFile($expectedFile, $actualString, $message = '', $canonicalize = false, $ignoreCase = false)
    {
        self::$testCase->assertStringNotEqualsFile($expectedFile, $actualString, $message, $canonicalize, $ignoreCase);
    }

    /**
     * Asserts that a file exists.
     *
     * @param string $filename
     * @param string $message
     * @since  Method available since Release 3.0.0
     */
    public static function assertFileExists($filename, $message = '')
    {
        self::$testCase->assertFileExists($filename, $message);
    }

    /**
     * Asserts that a file does not exist.
     *
     * @param string $filename
     * @param string $message
     * @since  Method available since Release 3.0.0
     */
    public static function assertFileNotExists($filename, $message = '')
    {
        self::$testCase->assertFileNotExists($filename, $message);
    }

    /**
     * Asserts that a condition is true.
     *
     * @param  boolean $condition
     * @param  string $message
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    public static function assertTrue($condition, $message = '')
    {
        self::$testCase->assertTrue($condition, $message);
    }

    /**
     * Asserts that a condition is not true.
     *
     * @param  boolean $condition
     * @param  string $message
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    public static function assertNotTrue($condition, $message = '')
    {
        self::$testCase->assertNotTrue($condition, $message);
    }

    /**
     * Asserts that a condition is false.
     *
     * @param  boolean $condition
     * @param  string $message
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    public static function assertFalse($condition, $message = '')
    {
        self::$testCase->assertFalse($condition, $message);
    }

    /**
     * Asserts that a condition is not false.
     *
     * @param  boolean $condition
     * @param  string $message
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    public static function assertnotfalse($condition, $message = '')
    {
        self::$testCase->assertnotfalse($condition, $message);
    }

    /**
     * Asserts that a variable is not null.
     *
     * @param mixed $actual
     * @param string $message
     */
    public static function assertNotNull($actual, $message = '')
    {
        self::$testCase->assertNotNull($actual, $message);
    }

    /**
     * Asserts that a variable is null.
     *
     * @param mixed $actual
     * @param string $message
     */
    public static function assertNull($actual, $message = '')
    {
        self::$testCase->assertNull($actual, $message);
    }

    /**
     * Asserts that a class has a specified attribute.
     *
     * @param string $attributeName
     * @param string $className
     * @param string $message
     * @since  Method available since Release 3.1.0
     */
    public static function assertClassHasAttribute($attributeName, $className, $message = '')
    {
        self::$testCase->assertClassHasAttribute($attributeName, $className, $message);
    }

    /**
     * Asserts that a class does not have a specified attribute.
     *
     * @param string $attributeName
     * @param string $className
     * @param string $message
     * @since  Method available since Release 3.1.0
     */
    public static function assertClassNotHasAttribute($attributeName, $className, $message = '')
    {
        self::$testCase->assertClassNotHasAttribute($attributeName, $className, $message);
    }

    /**
     * Asserts that a class has a specified static attribute.
     *
     * @param string $attributeName
     * @param string $className
     * @param string $message
     * @since  Method available since Release 3.1.0
     */
    public static function assertClassHasStaticAttribute($attributeName, $className, $message = '')
    {
    }

    /**
     * Asserts that a class does not have a specified static attribute.
     *
     * @param string $attributeName
     * @param string $className
     * @param string $message
     * @since  Method available since Release 3.1.0
     */
    public static function assertClassNotHasStaticAttribute($attributeName, $className, $message = '')
    {
        self::$testCase->assertClassNotHasStaticAttribute($attributeName, $className, $message);
    }

    /**
     * Asserts that an object has a specified attribute.
     *
     * @param string $attributeName
     * @param object $object
     * @param string $message
     * @since  Method available since Release 3.0.0
     */
    public static function assertObjectHasAttribute($attributeName, $object, $message = '')
    {
        self::$testCase->assertObjectHasAttribute($attributeName, $object, $message);
    }

    /**
     * Asserts that an object does not have a specified attribute.
     *
     * @param string $attributeName
     * @param object $object
     * @param string $message
     * @since  Method available since Release 3.0.0
     */
    public static function assertObjectNotHasAttribute($attributeName, $object, $message = '')
    {
        self::$testCase->assertObjectNotHasAttribute($attributeName, $object, $message);
    }

    /**
     * Asserts that two variables have the same type and value.
     * Used on objects, it asserts that two variables reference
     * the same object.
     *
     * @param mixed $expected
     * @param mixed $actual
     * @param string $message
     */
    public static function assertSame($expected, $actual, $message = '')
    {
        self::$testCase->assertSame($expected, $actual, $message);
    }

    /**
     * Asserts that a variable and an attribute of an object have the same type
     * and value.
     *
     * @param mixed $expected
     * @param string $actualAttributeName
     * @param object $actualClassOrObject
     * @param string $message
     */
    public static function assertAttributeSame($expected, $actualAttributeName, $actualClassOrObject, $message = '')
    {
        self::$testCase->assertAttributeSame($expected, $actualAttributeName, $actualClassOrObject, $message);
    }

    /**
     * Asserts that two variables do not have the same type and value.
     * Used on objects, it asserts that two variables do not reference
     * the same object.
     *
     * @param mixed $expected
     * @param mixed $actual
     * @param string $message
     */
    public static function assertNotSame($expected, $actual, $message = '')
    {
        self::$testCase->assertNotSame($expected, $actual, $message);
    }

    /**
     * Asserts that a variable and an attribute of an object do not have the
     * same type and value.
     *
     * @param mixed $expected
     * @param string $actualAttributeName
     * @param object $actualClassOrObject
     * @param string $message
     */
    public static function assertAttributeNotSame($expected, $actualAttributeName, $actualClassOrObject, $message = '')
    {
        self::$testCase->assertAttributeNotSame($expected, $actualAttributeName, $actualClassOrObject, $message);
    }

    /**
     * Asserts that a variable is of a given type.
     *
     * @param string $expected
     * @param mixed $actual
     * @param string $message
     * @since Method available since Release 3.5.0
     */
    public static function assertInstanceOf($expected, $actual, $message = '')
    {
        self::$testCase->assertInstanceOf($expected, $actual, $message);
    }

    /**
     * Asserts that an attribute is of a given type.
     *
     * @param string $expected
     * @param string $attributeName
     * @param mixed $classOrObject
     * @param string $message
     * @since Method available since Release 3.5.0
     */
    public static function assertAttributeInstanceOf($expected, $attributeName, $classOrObject, $message = '')
    {
        self::$testCase->assertAttributeInstanceOf($expected, $attributeName, $classOrObject, $message);
    }

    /**
     * Asserts that a variable is not of a given type.
     *
     * @param string $expected
     * @param mixed $actual
     * @param string $message
     * @since Method available since Release 3.5.0
     */
    public static function assertNotInstanceOf($expected, $actual, $message = '')
    {
        self::$testCase->assertNotInstanceOf($expected, $actual, $message);
    }

    /**
     * Asserts that an attribute is of a given type.
     *
     * @param string $expected
     * @param string $attributeName
     * @param mixed $classOrObject
     * @param string $message
     * @since Method available since Release 3.5.0
     */
    public static function assertAttributeNotInstanceOf($expected, $attributeName, $classOrObject, $message = '')
    {
        self::$testCase->assertAttributeNotInstanceOf($expected, $attributeName, $classOrObject, $message);
    }

    /**
     * Asserts that a variable is of a given type.
     *
     * @param string $expected
     * @param mixed $actual
     * @param string $message
     * @since Method available since Release 3.5.0
     */
    public static function assertInternalType($expected, $actual, $message = '')
    {
        self::$testCase->assertInternalType($expected, $actual, $message);
    }

    /**
     * Asserts that an attribute is of a given type.
     *
     * @param string $expected
     * @param string $attributeName
     * @param mixed $classOrObject
     * @param string $message
     * @since Method available since Release 3.5.0
     */
    public static function assertAttributeInternalType($expected, $attributeName, $classOrObject, $message = '')
    {
        self::$testCase->assertAttributeInternalType($expected, $attributeName, $classOrObject, $message);
    }

    /**
     * Asserts that a variable is not of a given type.
     *
     * @param string $expected
     * @param mixed $actual
     * @param string $message
     * @since Method available since Release 3.5.0
     */
    public static function assertNotInternalType($expected, $actual, $message = '')
    {
        self::$testCase->assertNotInternalType($expected, $actual, $message);
    }

    /**
     * Asserts that an attribute is of a given type.
     *
     * @param string $expected
     * @param string $attributeName
     * @param mixed $classOrObject
     * @param string $message
     * @since Method available since Release 3.5.0
     */
    public static function assertAttributeNotInternalType($expected, $attributeName, $classOrObject, $message = '')
    {
        self::$testCase->assertAttributeNotInternalType($expected, $attributeName, $classOrObject, $message);
    }

    /**
     * Asserts that a string matches a given regular expression.
     *
     * @param string $pattern
     * @param string $string
     * @param string $message
     */
    public static function assertRegExp($pattern, $string, $message = '')
    {
        self::$testCase->assertRegExp($pattern, $string, $message);
    }

    /**
     * Asserts that a string does not match a given regular expression.
     *
     * @param string $pattern
     * @param string $string
     * @param string $message
     * @since  Method available since Release 2.1.0
     */
    public static function assertNotRegExp($pattern, $string, $message = '')
    {
        self::$testCase->assertNotRegExp($pattern, $string, $message);
    }

    /**
     * Assert that the size of two arrays (or `Countable` or `Traversable` objects)
     * is the same.
     *
     * @param array|Countable|Traversable $expected
     * @param array|Countable|Traversable $actual
     * @param string $message
     */
    public static function assertSameSize($expected, $actual, $message = '')
    {
        self::$testCase->assertSameSize($expected, $actual, $message);
    }

    /**
     * Assert that the size of two arrays (or `Countable` or `Traversable` objects)
     * is not the same.
     *
     * @param array|Countable|Traversable $expected
     * @param array|Countable|Traversable $actual
     * @param string $message
     */
    public static function assertNotSameSize($expected, $actual, $message = '')
    {
        self::$testCase->assertNotSameSize($expected, $actual, $message);
    }

    /**
     * Asserts that a string matches a given format string.
     *
     * @param string $format
     * @param string $string
     * @param string $message
     * @since  Method available since Release 3.5.0
     */
    public static function assertStringMatchesFormat($format, $string, $message = '')
    {
        self::$testCase->assertStringMatchesFormat($format, $string, $message);
    }

    /**
     * Asserts that a string does not match a given format string.
     *
     * @param string $format
     * @param string $string
     * @param string $message
     * @since  Method available since Release 3.5.0
     */
    public static function assertStringNotMatchesFormat($format, $string, $message = '')
    {
        self::$testCase->assertStringNotMatchesFormat($format, $string, $message);
    }

    /**
     * Asserts that a string matches a given format file.
     *
     * @param string $formatFile
     * @param string $string
     * @param string $message
     * @since  Method available since Release 3.5.0
     */
    public static function assertStringMatchesFormatFile($formatFile, $string, $message = '')
    {
        self::$testCase->assertStringMatchesFormatFile($formatFile, $string, $message);
    }

    /**
     * Asserts that a string does not match a given format string.
     *
     * @param string $formatFile
     * @param string $string
     * @param string $message
     * @since  Method available since Release 3.5.0
     */
    public static function assertStringNotMatchesFormatFile($formatFile, $string, $message = '')
    {
        self::$testCase->assertStringNotMatchesFormatFile($formatFile, $string, $message);
    }

    /**
     * Asserts that a string starts with a given prefix.
     *
     * @param string $prefix
     * @param string $string
     * @param string $message
     * @since  Method available since Release 3.4.0
     */
    public static function assertStringStartsWith($prefix, $string, $message = '')
    {
        self::$testCase->assertStringStartsWith($prefix, $string, $message);
    }

    /**
     * Asserts that a string starts not with a given prefix.
     *
     * @param string $prefix
     * @param string $string
     * @param string $message
     * @since  Method available since Release 3.4.0
     */
    public static function assertStringStartsNotWith($prefix, $string, $message = '')
    {
        self::$testCase->assertStringStartsNotWith($prefix, $string, $message);
    }

    /**
     * Asserts that a string ends with a given suffix.
     *
     * @param string $suffix
     * @param string $string
     * @param string $message
     * @since  Method available since Release 3.4.0
     */
    public static function assertStringEndsWith($suffix, $string, $message = '')
    {
        self::$testCase->assertStringEndsWith($suffix, $string, $message);
    }

    /**
     * Asserts that a string ends not with a given suffix.
     *
     * @param string $suffix
     * @param string $string
     * @param string $message
     * @since  Method available since Release 3.4.0
     */
    public static function assertStringEndsNotWith($suffix, $string, $message = '')
    {
        self::$testCase->assertStringEndsNotWith($suffix, $string, $message);
    }

    /**
     * Asserts that two XML files are equal.
     *
     * @param string $expectedFile
     * @param string $actualFile
     * @param string $message
     * @since  Method available since Release 3.1.0
     */
    public static function assertXmlFileEqualsXmlFile($expectedFile, $actualFile, $message = '')
    {
        self::$testCase->assertXmlFileEqualsXmlFile($expectedFile, $actualFile, $message);
    }

    /**
     * Asserts that two XML files are not equal.
     *
     * @param string $expectedFile
     * @param string $actualFile
     * @param string $message
     * @since  Method available since Release 3.1.0
     */
    public static function assertXmlFileNotEqualsXmlFile($expectedFile, $actualFile, $message = '')
    {
        self::$testCase->assertXmlFileNotEqualsXmlFile($expectedFile, $actualFile, $message);
    }

    /**
     * Asserts that two XML documents are equal.
     *
     * @param string $expectedFile
     * @param string $actualXml
     * @param string $message
     * @since  Method available since Release 3.3.0
     */
    public static function assertXmlStringEqualsXmlFile($expectedFile, $actualXml, $message = '')
    {
        self::$testCase->assertXmlStringEqualsXmlFile($expectedFile, $actualXml, $message);
    }

    /**
     * Asserts that two XML documents are not equal.
     *
     * @param string $expectedFile
     * @param string $actualXml
     * @param string $message
     * @since  Method available since Release 3.3.0
     */
    public static function assertXmlStringNotEqualsXmlFile($expectedFile, $actualXml, $message = '')
    {
        self::$testCase->assertXmlStringNotEqualsXmlFile($expectedFile, $actualXml, $message);
    }

    /**
     * Asserts that two XML documents are equal.
     *
     * @param string $expectedXml
     * @param string $actualXml
     * @param string $message
     * @since  Method available since Release 3.1.0
     */
    public static function assertXmlStringEqualsXmlString($expectedXml, $actualXml, $message = '')
    {
        self::$testCase->assertXmlStringEqualsXmlString($expectedXml, $actualXml, $message);
    }

    /**
     * Asserts that two XML documents are not equal.
     *
     * @param string $expectedXml
     * @param string $actualXml
     * @param string $message
     * @since  Method available since Release 3.1.0
     */
    public static function assertXmlStringNotEqualsXmlString($expectedXml, $actualXml, $message = '')
    {
        self::$testCase->assertXmlStringNotEqualsXmlString($expectedXml, $actualXml, $message);
    }

    /**
     * Asserts that a hierarchy of DOMElements matches.
     *
     * @param DOMElement $expectedElement
     * @param DOMElement $actualElement
     * @param boolean $checkAttributes
     * @param string $message
     * @author Mattis Stordalen Flister <mattis@xait.no>
     * @since  Method available since Release 3.3.0
     */
    public static function assertEqualXMLStructure(DOMElement $expectedElement, DOMElement $actualElement, $checkAttributes = false, $message = '')
    {
        self::$testCase->assertEqualXMLStructure($expectedElement, $actualElement, $checkAttributes, $message);
    }

    /**
     * Assert the presence, absence, or count of elements in a document matching
     * the CSS $selector, regardless of the contents of those elements.
     *
     * The first argument, $selector, is the CSS selector used to match
     * the elements in the $actual document.
     *
     * The second argument, $count, can be either boolean or numeric.
     * When boolean, it asserts for presence of elements matching the selector
     * (true) or absence of elements (false).
     * When numeric, it asserts the count of elements.
     *
     * assertSelectCount("#binder", true, $xml);  // any?
     * assertSelectCount(".binder", 3, $xml);     // exactly 3?
     *
     * @param array $selector
     * @param integer|boolean|array $count
     * @param mixed $actual
     * @param string $message
     * @param boolean $isHtml
     * @since  Method available since Release 3.3.0
     * @author Mike Naberezny <mike@maintainable.com>
     * @author Derek DeVries <derek@maintainable.com>
     * @deprecated
     */
    public static function assertSelectCount($selector, $count, $actual, $message = '', $isHtml = true)
    {
        self::$testCase->assertSelectCount($selector, $count, $actual, $message, $isHtml);
    }

    /**
     * assertSelectRegExp("#binder .name", "/Mike|Derek/", true, $xml); // any?
     * assertSelectRegExp("#binder .name", "/Mike|Derek/", 3, $xml);    // 3?
     *
     * @param array $selector
     * @param string $pattern
     * @param integer|boolean|array $count
     * @param mixed $actual
     * @param string $message
     * @param boolean $isHtml
     * @since  Method available since Release 3.3.0
     * @author Mike Naberezny <mike@maintainable.com>
     * @author Derek DeVries <derek@maintainable.com>
     * @deprecated
     */
    public static function assertSelectRegExp($selector, $pattern, $count, $actual, $message = '', $isHtml = true)
    {
        self::$testCase->assertSelectRegExp($selector, $pattern, $count, $actual, $message, $isHtml);
    }

    /**
     * assertSelectEquals("#binder .name", "Chuck", true,  $xml);  // any?
     * assertSelectEquals("#binder .name", "Chuck", false, $xml);  // none?
     *
     * @param array $selector
     * @param string $content
     * @param integer|boolean|array $count
     * @param mixed $actual
     * @param string $message
     * @param boolean $isHtml
     * @since  Method available since Release 3.3.0
     * @author Mike Naberezny <mike@maintainable.com>
     * @author Derek DeVries <derek@maintainable.com>
     * @deprecated
     */
    public static function assertSelectEquals($selector, $content, $count, $actual, $message = '', $isHtml = true)
    {
        self::$testCase->assertSelectEquals($selector, $content, $count, $actual, $message, $isHtml);
    }

    /**
     * Evaluate an HTML or XML string and assert its structure and/or contents.
     *
     * The first argument ($matcher) is an associative array that specifies the
     * match criteria for the assertion:
     *
     *  - `id`           : the node with the given id attribute must match the
     *                     corresponding value.
     *  - `tag`          : the node type must match the corresponding value.
     *  - `attributes`   : a hash. The node's attributes must match the
     *                     corresponding values in the hash.
     *  - `content`      : The text content must match the given value.
     *  - `parent`       : a hash. The node's parent must match the
     *                     corresponding hash.
     *  - `child`        : a hash. At least one of the node's immediate children
     *                     must meet the criteria described by the hash.
     *  - `ancestor`     : a hash. At least one of the node's ancestors must
     *                     meet the criteria described by the hash.
     *  - `descendant`   : a hash. At least one of the node's descendants must
     *                     meet the criteria described by the hash.
     *  - `children`     : a hash, for counting children of a node.
     *                     Accepts the keys:
     *    - `count`        : a number which must equal the number of children
     *                       that match
     *    - `less_than`    : the number of matching children must be greater
     *                       than this number
     *    - `greater_than` : the number of matching children must be less than
     *                       this number
     *    - `only`         : another hash consisting of the keys to use to match
     *                       on the children, and only matching children will be
     *                       counted
     *
     * <code>
     * // Matcher that asserts that there is an element with an id="my_id".
     * $matcher = array('id' => 'my_id');
     *
     * // Matcher that asserts that there is a "span" tag.
     * $matcher = array('tag' => 'span');
     *
     * // Matcher that asserts that there is a "span" tag with the content
     * // "Hello World".
     * $matcher = array('tag' => 'span', 'content' => 'Hello World');
     *
     * // Matcher that asserts that there is a "span" tag with content matching
     * // the regular expression pattern.
     * $matcher = array('tag' => 'span', 'content' => 'regexp:/Try P(HP|ython)/');
     *
     * // Matcher that asserts that there is a "span" with an "list" class
     * // attribute.
     * $matcher = array(
     *   'tag'        => 'span',
     *   'attributes' => array('class' => 'list')
     * );
     *
     * // Matcher that asserts that there is a "span" inside of a "div".
     * $matcher = array(
     *   'tag'    => 'span',
     *   'parent' => array('tag' => 'div')
     * );
     *
     * // Matcher that asserts that there is a "span" somewhere inside a
     * // "table".
     * $matcher = array(
     *   'tag'      => 'span',
     *   'ancestor' => array('tag' => 'table')
     * );
     *
     * // Matcher that asserts that there is a "span" with at least one "em"
     * // child.
     * $matcher = array(
     *   'tag'   => 'span',
     *   'child' => array('tag' => 'em')
     * );
     *
     * // Matcher that asserts that there is a "span" containing a (possibly
     * // nested) "strong" tag.
     * $matcher = array(
     *   'tag'        => 'span',
     *   'descendant' => array('tag' => 'strong')
     * );
     *
     * // Matcher that asserts that there is a "span" containing 5-10 "em" tags
     * // as immediate children.
     * $matcher = array(
     *   'tag'      => 'span',
     *   'children' => array(
     *     'less_than'    => 11,
     *     'greater_than' => 4,
     *     'only'         => array('tag' => 'em')
     *   )
     * );
     *
     * // Matcher that asserts that there is a "div", with an "ul" ancestor and
     * // a "li" parent (with class="enum"), and containing a "span" descendant
     * // that contains an element with id="my_test" and the text "Hello World".
     * $matcher = array(
     *   'tag'        => 'div',
     *   'ancestor'   => array('tag' => 'ul'),
     *   'parent'     => array(
     *     'tag'        => 'li',
     *     'attributes' => array('class' => 'enum')
     *   ),
     *   'descendant' => array(
     *     'tag'   => 'span',
     *     'child' => array(
     *       'id'      => 'my_test',
     *       'content' => 'Hello World'
     *     )
     *   )
     * );
     *
     * // Use assertTag() to apply a $matcher to a piece of $html.
     * $this->assertTag($matcher, $html);
     *
     * // Use assertTag() to apply a $matcher to a piece of $xml.
     * $this->assertTag($matcher, $xml, '', false);
     * </code>
     *
     * The second argument ($actual) is a string containing either HTML or
     * XML text to be tested.
     *
     * The third argument ($message) is an optional message that will be
     * used if the assertion fails.
     *
     * The fourth argument ($html) is an optional flag specifying whether
     * to load the $actual string into a DOMDocument using the HTML or
     * XML load strategy.  It is true by default, which assumes the HTML
     * load strategy.  In many cases, this will be acceptable for XML as well.
     *
     * @param array $matcher
     * @param string $actual
     * @param string $message
     * @param boolean $isHtml
     * @since  Method available since Release 3.3.0
     * @author Mike Naberezny <mike@maintainable.com>
     * @author Derek DeVries <derek@maintainable.com>
     * @deprecated
     */
    public static function assertTag($matcher, $actual, $message = '', $isHtml = true)
    {
        self::$testCase->assertTag($matcher, $actual, $message, $isHtml);
    }

    /**
     * This assertion is the exact opposite of assertTag().
     *
     * Rather than asserting that $matcher results in a match, it asserts that
     * $matcher does not match.
     *
     * @param array $matcher
     * @param string $actual
     * @param string $message
     * @param boolean $isHtml
     * @since  Method available since Release 3.3.0
     * @author Mike Naberezny <mike@maintainable.com>
     * @author Derek DeVries <derek@maintainable.com>
     * @deprecated
     */
    public static function assertNotTag($matcher, $actual, $message = '', $isHtml = true)
    {
        self::$testCase->assertNotTag($matcher, $actual, $message, $isHtml);
    }

    /**
     * Evaluates a PHPUnit_Framework_Constraint matcher object.
     *
     * @param mixed $value
     * @param PHPUnit_Framework_Constraint $constraint
     * @param string $message
     * @since  Method available since Release 3.0.0
     */
    public static function assertThat($value, PHPUnit_Framework_Constraint $constraint, $message = '')
    {
        self::$testCase->assertThat($value, $constraint, $message);
    }

    /**
     * Asserts that a string is a valid JSON string.
     *
     * @param string $actualJson
     * @param string $message
     * @since  Method available since Release 3.7.20
     */
    public static function assertJson($actualJson, $message = '')
    {
        self::$testCase->assertJson($actualJson, $message);
    }

    /**
     * Asserts that two given JSON encoded objects or arrays are equal.
     *
     * @param string $expectedJson
     * @param string $actualJson
     * @param string $message
     */
    public static function assertJsonStringEqualsJsonString($expectedJson, $actualJson, $message = '')
    {
        self::$testCase->assertJsonStringEqualsJsonString($expectedJson, $actualJson, $message);
    }

    /**
     * Asserts that two given JSON encoded objects or arrays are not equal.
     *
     * @param string $expectedJson
     * @param string $actualJson
     * @param string $message
     */
    public static function assertJsonStringNotEqualsJsonString($expectedJson, $actualJson, $message = '')
    {
        self::$testCase->assertJsonStringNotEqualsJsonString($expectedJson, $actualJson, $message);
    }

    /**
     * Asserts that the generated JSON encoded object and the content of the given file are equal.
     *
     * @param string $expectedFile
     * @param string $actualJson
     * @param string $message
     */
    public static function assertJsonStringEqualsJsonFile($expectedFile, $actualJson, $message = '')
    {
        self::$testCase->assertJsonStringEqualsJsonFile($expectedFile, $actualJson, $message);
    }

    /**
     * Asserts that the generated JSON encoded object and the content of the given file are not equal.
     *
     * @param string $expectedFile
     * @param string $actualJson
     * @param string $message
     */
    public static function assertJsonStringNotEqualsJsonFile($expectedFile, $actualJson, $message = '')
    {
        self::$testCase->assertJsonStringNotEqualsJsonFile($expectedFile, $actualJson, $message);
    }

    /**
     * Asserts that two JSON files are not equal.
     *
     * @param string $expectedFile
     * @param string $actualFile
     * @param string $message
     */
    public static function assertJsonFileNotEqualsJsonFile($expectedFile, $actualFile, $message = '')
    {
        self::$testCase->assertJsonFileNotEqualsJsonFile($expectedFile, $actualFile, $message);
    }

    /**
     * Asserts that two JSON files are equal.
     *
     * @param string $expectedFile
     * @param string $actualFile
     * @param string $message
     */
    public static function assertJsonFileEqualsJsonFile($expectedFile, $actualFile, $message = '')
    {
        self::$testCase->assertJsonFileEqualsJsonFile($expectedFile, $actualFile, $message);
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_And matcher object.
     *
     * @return PHPUnit_Framework_Constraint_And
     * @since  Method available since Release 3.0.0
     */
    public static function logicalAnd()
    {
        return self::$testCase->logicalAnd();
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_Or matcher object.
     *
     * @return PHPUnit_Framework_Constraint_Or
     * @since  Method available since Release 3.0.0
     */
    public static function logicalOr()
    {
        return self::$testCase->logicalOr();
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_Not matcher object.
     *
     * @param  PHPUnit_Framework_Constraint $constraint
     * @return PHPUnit_Framework_Constraint_Not
     * @since  Method available since Release 3.0.0
     */
    public static function logicalNot(PHPUnit_Framework_Constraint $constraint)
    {
        return self::$testCase->logicalNot($constraint);
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_Xor matcher object.
     *
     * @return PHPUnit_Framework_Constraint_Xor
     * @since  Method available since Release 3.0.0
     */
    public static function logicalXor()
    {
        return self::$testCase->logicalXor();
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_IsAnything matcher object.
     *
     * @return PHPUnit_Framework_Constraint_IsAnything
     * @since  Method available since Release 3.0.0
     */
    public static function anything()
    {
        return self::$testCase->anything();
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_IsTrue matcher object.
     *
     * @return PHPUnit_Framework_Constraint_IsTrue
     * @since  Method available since Release 3.3.0
     */
    public static function isTrue()
    {
        return self::$testCase->isTrue();
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_Callback matcher object.
     *
     * @param  callable $callback
     * @return PHPUnit_Framework_Constraint_Callback
     */
    public static function callback($callback)
    {
        return self::$testCase->callback($callback);
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_IsFalse matcher object.
     *
     * @return PHPUnit_Framework_Constraint_IsFalse
     * @since  Method available since Release 3.3.0
     */
    public static function isFalse()
    {
        return self::$testCase->isFalse();
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_IsJson matcher object.
     *
     * @return PHPUnit_Framework_Constraint_IsJson
     * @since  Method available since Release 3.7.20
     */
    public static function isJson()
    {
        return self::$testCase->isJson();
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_IsNull matcher object.
     *
     * @return PHPUnit_Framework_Constraint_IsNull
     * @since  Method available since Release 3.3.0
     */
    public static function isNull()
    {
        return self::$testCase->isNull();
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_Attribute matcher object.
     *
     * @param  PHPUnit_Framework_Constraint $constraint
     * @param  string $attributeName
     * @return PHPUnit_Framework_Constraint_Attribute
     * @since  Method available since Release 3.1.0
     */
    public static function attribute(PHPUnit_Framework_Constraint $constraint, $attributeName)
    {
        return self::$testCase->attribute($constraint, $attributeName);
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_TraversableContains matcher
     * object.
     *
     * @param  mixed $value
     * @param  boolean $checkForObjectIdentity
     * @param  boolean $checkForNonObjectIdentity
     * @return PHPUnit_Framework_Constraint_TraversableContains
     * @since  Method available since Release 3.0.0
     */
    public static function contains($value, $checkForObjectIdentity = true, $checkForNonObjectIdentity = false)
    {
        return self::$testCase->contains($value, $checkForObjectIdentity, $checkForNonObjectIdentity);
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_TraversableContainsOnly matcher
     * object.
     *
     * @param  string $type
     * @return PHPUnit_Framework_Constraint_TraversableContainsOnly
     * @since  Method available since Release 3.1.4
     */
    public static function containsOnly($type)
    {
        return self::$testCase->containsOnly($type);
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_TraversableContainsOnly matcher
     * object.
     *
     * @param  string $classname
     * @return PHPUnit_Framework_Constraint_TraversableContainsOnly
     */
    public static function containsOnlyInstancesOf($classname)
    {
        return self::$testCase->containsOnlyInstancesOf($classname);
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_ArrayHasKey matcher object.
     *
     * @param  mixed $key
     * @return PHPUnit_Framework_Constraint_ArrayHasKey
     * @since  Method available since Release 3.0.0
     */
    public static function arrayHasKey($key)
    {
        return self::$testCase->arrayHasKey($key);
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_IsEqual matcher object.
     *
     * @param  mixed $value
     * @param  float $delta
     * @param  integer $maxDepth
     * @param  boolean $canonicalize
     * @param  boolean $ignoreCase
     * @return PHPUnit_Framework_Constraint_IsEqual
     * @since  Method available since Release 3.0.0
     */
    public static function equalTo($value, $delta = 0.0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false)
    {
        return self::$testCase->equalTo($value, $delta, $maxDepth, $canonicalize, $ignoreCase);
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_IsEqual matcher object
     * that is wrapped in a PHPUnit_Framework_Constraint_Attribute matcher
     * object.
     *
     * @param  string $attributeName
     * @param  mixed $value
     * @param  float $delta
     * @param  integer $maxDepth
     * @param  boolean $canonicalize
     * @param  boolean $ignoreCase
     * @return PHPUnit_Framework_Constraint_Attribute
     * @since  Method available since Release 3.1.0
     */
    public static function attributeEqualTo($attributeName, $value, $delta = 0.0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false)
    {
        return self::$testCase->attributeEqualTo($attributeName, $value, $delta, $maxDepth, $canonicalize, $ignoreCase);
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_IsEmpty matcher object.
     *
     * @return PHPUnit_Framework_Constraint_IsEmpty
     * @since  Method available since Release 3.5.0
     */
    public static function isEmpty()
    {
        return self::$testCase->isEmpty();
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_FileExists matcher object.
     *
     * @return PHPUnit_Framework_Constraint_FileExists
     * @since  Method available since Release 3.0.0
     */
    public static function fileExists()
    {
        return self::$testCase->fileExists();
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_GreaterThan matcher object.
     *
     * @param  mixed $value
     * @return PHPUnit_Framework_Constraint_GreaterThan
     * @since  Method available since Release 3.0.0
     */
    public static function greaterThan($value)
    {
        return self::$testCase->greaterThan($value);
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_Or matcher object that wraps
     * a PHPUnit_Framework_Constraint_IsEqual and a
     * PHPUnit_Framework_Constraint_GreaterThan matcher object.
     *
     * @param  mixed $value
     * @return PHPUnit_Framework_Constraint_Or
     * @since  Method available since Release 3.1.0
     */
    public static function greaterThanOrEqual($value)
    {
        return self::$testCase->greaterThanOrEqual($value);
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_ClassHasAttribute matcher object.
     *
     * @param  string $attributeName
     * @return PHPUnit_Framework_Constraint_ClassHasAttribute
     * @since  Method available since Release 3.1.0
     */
    public static function classHasAttribute($attributeName)
    {
        return self::$testCase->classHasAttribute($attributeName);
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_ClassHasStaticAttribute matcher
     * object.
     *
     * @param  string $attributeName
     * @return PHPUnit_Framework_Constraint_ClassHasStaticAttribute
     * @since  Method available since Release 3.1.0
     */
    public static function classHasStaticAttribute($attributeName)
    {
        return self::$testCase->classHasStaticAttribute($attributeName);
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_ObjectHasAttribute matcher object.
     *
     * @param  string $attributeName
     * @return PHPUnit_Framework_Constraint_ObjectHasAttribute
     * @since  Method available since Release 3.0.0
     */
    public static function objectHasAttribute($attributeName)
    {
        self::$testCase->objectHasAttribute($attributeName);
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_IsIdentical matcher object.
     *
     * @param  mixed $value
     * @return PHPUnit_Framework_Constraint_IsIdentical
     * @since  Method available since Release 3.0.0
     */
    public static function identicalTo($value)
    {
        return self::$testCase->identicalTo($value);
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_IsInstanceOf matcher object.
     *
     * @param  string $className
     * @return PHPUnit_Framework_Constraint_IsInstanceOf
     * @since  Method available since Release 3.0.0
     */
    public static function isInstanceOf($className)
    {
        return self::$testCase->isInstanceOf($className);
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_IsType matcher object.
     *
     * @param  string $type
     * @return PHPUnit_Framework_Constraint_IsType
     * @since  Method available since Release 3.0.0
     */
    public static function isType($type)
    {
        return self::$testCase->isType($type);
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_LessThan matcher object.
     *
     * @param  mixed $value
     * @return PHPUnit_Framework_Constraint_LessThan
     * @since  Method available since Release 3.0.0
     */
    public static function lessThan($value)
    {
        return self::$testCase->lessThan($value);
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_Or matcher object that wraps
     * a PHPUnit_Framework_Constraint_IsEqual and a
     * PHPUnit_Framework_Constraint_LessThan matcher object.
     *
     * @param  mixed $value
     * @return PHPUnit_Framework_Constraint_Or
     * @since  Method available since Release 3.1.0
     */
    public static function lessThanOrEqual($value)
    {
        return self::$testCase->lessThanOrEqual($value);
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_PCREMatch matcher object.
     *
     * @param  string $pattern
     * @return PHPUnit_Framework_Constraint_PCREMatch
     * @since  Method available since Release 3.0.0
     */
    public static function matchesRegularExpression($pattern)
    {
        return new self::$testCase->matchesRegularExpression($pattern);
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_StringMatches matcher object.
     *
     * @param  string $string
     * @return PHPUnit_Framework_Constraint_StringMatches
     * @since  Method available since Release 3.5.0
     */
    public static function matches($string)
    {
        return self::$testCase->matches($string);
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_StringStartsWith matcher object.
     *
     * @param  mixed $prefix
     * @return PHPUnit_Framework_Constraint_StringStartsWith
     * @since  Method available since Release 3.4.0
     */
    public static function stringStartsWith($prefix)
    {
        return self::$testCase->stringStartsWith($prefix);
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_StringContains matcher object.
     *
     * @param  string $string
     * @param  boolean $case
     * @return PHPUnit_Framework_Constraint_StringContains
     * @since  Method available since Release 3.0.0
     */
    public static function stringContains($string, $case = true)
    {
        return self::$testCase->stringContains($string, $case);
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_StringEndsWith matcher object.
     *
     * @param  mixed $suffix
     * @return PHPUnit_Framework_Constraint_StringEndsWith
     * @since  Method available since Release 3.4.0
     */
    public static function stringEndsWith($suffix)
    {
        return new self::$testCase->stringEndsWith($suffix);
    }

    /**
     * Returns a PHPUnit_Framework_Constraint_Count matcher object.
     *
     * @param  int $count
     * @return PHPUnit_Framework_Constraint_Count
     */
    public static function countOf($count)
    {
        return new self::$testCase->countOf($count);
    }

    /**
     * Fails a test with the given message.
     *
     * @param  string $message
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    public static function fail($message = '')
    {
        return new self::$testCase->fail($message);
    }

    /**
     * Returns the value of an attribute of a class or an object.
     * This also works for attributes that are declared protected or private.
     *
     * @param  mixed $classOrObject
     * @param  string $attributeName
     * @return mixed
     * @throws PHPUnit_Framework_Exception
     */
    public static function readAttribute($classOrObject, $attributeName)
    {
        self::$testCase->readAttribute($classOrObject, $attributeName);
    }

    /**
     * Returns the value of a static attribute.
     * This also works for attributes that are declared protected or private.
     *
     * @param  string $className
     * @param  string $attributeName
     * @return mixed
     * @throws PHPUnit_Framework_Exception
     * @since  Method available since Release 4.0.0
     */
    public static function getStaticAttribute($className, $attributeName)
    {
        return self::$testCase->getStaticAttribute($className, $attributeName);
    }

    /**
     * Returns the value of an object's attribute.
     * This also works for attributes that are declared protected or private.
     *
     * @param  object $object
     * @param  string $attributeName
     * @return mixed
     * @throws PHPUnit_Framework_Exception
     * @since  Method available since Release 4.0.0
     */
    public static function getObjectAttribute($object, $attributeName)
    {
        return self::$testCase->getObjectAttribute($object, $attributeName);
    }

    /**
     * Mark the test as incomplete.
     *
     * @param  string $message
     * @throws PHPUnit_Framework_IncompleteTestError
     * @since  Method available since Release 3.0.0
     */
    public static function markTestIncomplete($message = '')
    {
        self::$testCase->markTestIncomplete($message);
    }

    /**
     * Mark the test as skipped.
     *
     * @param  string $message
     * @throws PHPUnit_Framework_SkippedTestError
     * @since  Method available since Release 3.0.0
     */
    public static function markTestSkipped($message = '')
    {
        self::$testCase->markTestSkipped($message);
    }

    /**
     * Return the current assertion count.
     *
     * @return integer
     * @since  Method available since Release 3.3.3
     */
    public static function getCount()
    {
        return self::$testCase->getCount();
    }

    /**
     * Reset the assertion counter.
     *
     * @since  Method available since Release 3.3.3
     */
    public static function resetCount()
    {
        self::$testCase->resetCount();
    }
}