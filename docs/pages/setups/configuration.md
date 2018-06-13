---
title: Configuration
url: /setups/configuration.html
permalink: /setups/configuration.html
sidebar_link: true
---

## Introduction 
To configure Function Mocker to work in specific cases and in combination with different testing framework see the [Different setups section](/different-setups.html).

## Testing environments
Function Mocker will work whether WordPress is loaded in the same variable scope as the test or not.  
But when Function Mocker is used *without* loading WordPress, and any other theme and plugin with it, some utility functions and basic functionalities might be missing.  
Function Mocker loads, by default, a minimal WordPress "environment" that will code tested **without loading WordPress** to call commonly used functions like `add_filter` or `add_action`, `apply_filters` and `do_action`, formatting functions like `trailingslashit` and `untrailingslashit`, localization functions like `__` and `_e` and utility functions like `wp_list_filter` and `wp_list_pluck`; the functions listed here are merely an example but more are included.  
To setup a testing environment completely clean of any WordPress defined function just call the `FunctionMocker::init()` method specifying an emtpy array in the `env` option.
As an example, in your tests bootstrap file:

```php
// init Function Mocker
\tad\FunctionMocker\FunctionMocker::init( [
	'cache-path'            => __DIR__ . '/_cache',
	'whitelist'             => [ __DIR__, dirname( __DIR__ ) . '/src' ],
	'redefinable-internals' => [ 'time' ],
	'env' => [], // this will prevent the default WordPress environment from being loaded
] );
```

Testing environments solve the problem of avoiding duplication, in the shipped code or in the tests for the shipped code, of functions and methods that are not subject to test but that are part of the environment itself; as an example it *might* happen to have to stub, mock or spy the `apply_filters` method in a small number of tests but, for the most part, .
There is no way for Function Mocker to keep up with all the needs of all the testing setups and all the code that's out there so Function Mocker comes with its own environment generation tool.  

## Environment generation
T
