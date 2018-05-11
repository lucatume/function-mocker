---
title: Alternatives
url: alternatives.html
permalink: alternatives.html
sidebar_link: true
---

## Other good, similar projects
Function Mocker is by no means the only function mocking solution available.  
There are others that I'm listing below in no particular order:

* [Brain Monkey](!g) - another function mocking framework based on [Patchwork](!g patchwork2 php) that uses [Mockery](!g) to handle the mocking; made by a very good WordPress developer ([Giuseppe Mazzapica](!g github giuseppe mazzapica))
* [WP_Mock](!g) - another WordPress specific solution made by the good folks at [10up](http://10up.com/) specific for unit tests
* [AspectMock](!g) - made by the author of [Codeception](http://codeception.com/ "Codeception - BDD-style PHP testing."); with a more wide target and not aimed at WordPress in particular; based on the [Go! AOP framework](!g goaop/framework)

## Why another function mocking library then?
With all these good libraries why make another?  
Some reasons:

1. I want something specific to WordPress
2. I want to use [Prophecy](!g php prophecy) syntax
3. I want something I can seamlessly use in unit tests (where WordPress is not loaded at all) and integration tests (where WordPress is loaded before the tests and with it all its functions)

To my knowledge none of the above would provide me with all that so I've developed my own solution.

If you are still [check out the quickstart guide](/quickstart.md).