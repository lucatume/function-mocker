---
title: Alternatives
url: alternatives.html
permalink: alternatives.html
sidebar_link: true
---

## Other good, similar projects
Function Mocker is by no means the only function mocking solution available.  
There are others that I'm listing below in no particular order:

* [Brain Monkey][7661-0008] - another function mocking framework based on [Patchwork][7661-0002] that uses [Mockery][7661-0009] to handle the mocking; made by a very good WordPress developer ([Giuseppe Mazzapica][7661-0004])
* [WP_Mock][7661-0010] - another WordPress specific solution made by the good folks at [10up](http://10up.com/) specific for unit tests
* [AspectMock][7661-0005] - made by the author of [Codeception](http://codeception.com/ "Codeception - BDD-style PHP testing."); with a more wide target and not aimed at WordPress in particular; based on the [Go! AOP framework][7661-0006]

## Why another function mocking library then?
With all these good libraries why make another?  
Some reasons:

1. I want something specific to WordPress
2. I want to use [Prophecy][7661-0007] syntax
3. I want something I can seamlessly use in unit tests (where WordPress is not loaded at all) and integration tests (where WordPress is loaded before the tests and with it all its functions)
4. I do not want to stub/mock a growing number of infrastructure functions in my tests

To my knowledge none of the above would provide me with all that so I've developed my own solution; I might be wrong so check out each project and choose the one that suits your flow and setup the best.

If you are still here [check out the installation guide](installation.md).

[7661-0002]: http://patchwork2.org/
[7661-0004]: https://github.com/Giuseppe-Mazzapica
[7661-0005]: https://github.com/Codeception/AspectMock
[7661-0006]: http://go.aopphp.com/
[7661-0007]: https://github.com/phpspec/prophecy
[7661-0008]: https://github.com/Brain-WP/BrainMonkey
[7661-0009]: https://github.com/mockery/mockery
[7661-0010]: https://github.com/10up/wp_mock

