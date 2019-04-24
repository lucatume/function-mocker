Function Mocker should be installed as a developer dependency using [Composer](https://getcomposer.org/); from your WordPress project root folder (a plugin, a theme or a whole site) run:

```bash
composer install --dev lucatume/function-mocker
```

Function Mocker has two main dependencies and it will pull them when installing it:

* [Patchwork][0186-0001] - a monkey patching library for PHP
* [Prophecy][0186-0002] - [phpspec][0186-0003] own mocking engine

If you are using [PHPUnit](https://phpunit.de/ "PHPUnit – The PHP Testing Framework") the second will be installed with it, still it's good to know what is happening.

[0186-0001]: http://patchwork2.org/
[0186-0002]: https://github.com/phpspec/prophecy
[0186-0003]: http://www.phpspec.net/en/stable/

