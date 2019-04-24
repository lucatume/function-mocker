Version `2` of Function Mocker improves the base code and its API over version 1 but, to the extent of my best effort, should not introduce breaking changes in respect to version `1` **when it comes to function replacement**.  
The biggest change is the removal of the "mocking" engine to rely on [Prophecy][7219-0001] for any class method stubbing/mocking/spying.

## Removal of the mocking engine and replacement in tests
Version `1` did allow writing the following code:

```php
$mock = FunctionMocker::replace('SomeClass::someMethod', 23);
```

The `$mock` object was a [PHPUnit mock object][7219-0002] instance built on class method and woud provide the API documented in the [PHPUnit documentation][7219-0002].  
This use of Function Mocker was removed in version `2` and can be replace either by using PHPUnit mocks:

```php
// In a testcase...
$mock = $this->getMockBuilder('SomeClass')->getMock();
$mock->method('someMethod')->willReturn(23);
```

Or by using [Prophecy][7219-0001] that comes with PHPUnit:

```php
// In a testcase...
$prophecy = $this->prophesize('SomeClass');
$prophecy->someMethod()->willReturn(23);
$mock = $prophecy->reveal();
```

[7219-0001]: https://github.com/phpspec/prophecy
[7219-0002]: https://phpunit.readthedocs.io/en/8.0/test-doubles.html

