Function Mocker is not a testing framework but merely a testing tool meant to be used in the context of a fully-fledged testing framework.  
Starting from the [general configuration](general-configuration.md) you should be able to work out the pieces required to make function-mocker work with your test framework of choice.  

The repository [contains examples](https://github.com/lucatume/function-mocker/tree/master/examples) of how to set up a testing project using different and supported frameworks:

* [Codeception setup](https://github.com/lucatume/function-mocker/tree/master/examples/codeception) - how to setup Function Mocker to use it with [Codeception](http://codeception.com/ "Codeception - BDD-style PHP testing.").  
* [phpspec](https://github.com/lucatume/function-mocker/tree/master/examples/phpspec) - how to setup Function Mocker to use it with [phpspec](https://www.phpspec.net/en/stable/).  
* [PHPUnit](https://github.com/lucatume/function-mocker/tree/master/examples/phpunit) - how to setup Function Mocker to use it with [PhpUnit](https://phpunit.de/ "PHPUnit – The PHP Testing Framework").
* [WooCommerce](https://github.com/lucatume/function-mocker/tree/master/examples/woocommerce-env) - how to setup Function Mocker to run [PhpUnit](https://phpunit.de/ "PHPUnit – The PHP Testing Framework") tests for a [WooCommerce][8508-0001] plugin; this example does contain an example of environment generation too.  
* [wp-browser](https://github.com/lucatume/function-mocker/tree/master/examples/wp-browser) - how to setup Function Mocker to use it with [wp-browser](https://github.com/lucatume/wp-browser "lucatume/wp-browser · GitHub").
* [WordPress Core Suite](https://github.com/lucatume/function-mocker/tree/master/examples/wp-core-suite) - how to setup Function Mocker to use it with the [WordPress Core PHPUnit-based test suite][8508-0002].

If the framework you would like to use is not listed it does not mean that Function Mocker will not work with it; do not hesitate [to open an issue](https://github.com/lucatume/function-mocker/issues/new), or submit a pull-request, if you would like to see your testing framework of choice in this list.  

[8508-0001]: https://wordpress.org/plugins/woocommerce/
[8508-0002]: https://make.wordpress.org/core/handbook/testing/automated-testing/phpunit/

