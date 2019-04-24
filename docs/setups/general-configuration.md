## Introduction 
To configure Function Mocker to work in specific cases and in combination with different testing framework see the [Different setups section](index.md).

## Testing environments

### The built-in WordPress environment
Function Mocker will work whether WordPress is loaded in the same variable scope as the tests or not.  
When Function Mocker is used *without* loading WordPress, and any other theme and plugin with it, some utility functions and basic functionalities might be missing.  
Function Mocker loads, by default, a minimal WordPress "environment" that will provide "copies" of commonly used WordPress functions and classes **without loading WordPress**.  
Functions like `add_filter` or `add_action`, `apply_filters` and `do_action`, formatting functions like `trailingslashit` and `untrailingslashit`, localization functions like `__` and `_e` and utility functions like `wp_list_filter` and `wp_list_pluck` are at the base of much of plugins and themes code and this minimal environment will remove, from anyone willing to test WordPress in isolation, [at a unit level](../levels-of-testing.md) the chore of having to stub, mock or rewrite each and all these functions by hand.
You can prevent Function Mocker from loading this WordPress testing environment by initializing it with the `load-wp-env` argument set to `false`:

```php
// init Function Mocker
\tad\FunctionMocker\FunctionMocker::init( [
	'whitelist'             => [ __DIR__, dirname( __DIR__ ) . '/src' ],
	'redefinable-internals' => [ 'time' ],
	'load-wp-env' => false, // This will prevent the default WordPress environment from being loaded.
] );
```

The WordPress environment contains copies of the following functions:  

* `__return_empty_array`
* `__return_empty_string`
* `__return_false`
* `__return_null`
* `__return_true`
* `__return_zero`
* `_wp_call_all_hook`
* `_wp_filter_build_unique_id`
* `add_action`
* `add_filter`
* `apply_filters`
* `apply_filters_ref_array`
* `current_filter`
* `did_action`
* `do_action`
* `do_action_ref_array`
* `doing_action`
* `doing_filter`
* `has_action`
* `has_filter`
* `remove_action`
* `remove_filter`
* `trailingslashit`
* `untrailingslashit`
* `wp_list_filter`
* `wp_list_pluck`
* `wp_list_sort`

And of the following classes:  

* `WP_Hook`
* `WP_List_Util`

### Loading additional testing environments
You're not limited to loading the built-in WordPress environment: you can generate (see [Environment Generation](environment-generation.md)) your custom environments and load them replacing, or alongside, the built-in WordPress one.  
To define the list of testing environments to load you can set the `env` argument in the array passed to the `FunctionMocker::init()` method, like this:

```php
// init Function Mocker
\tad\FunctionMocker\FunctionMocker::init( [
	'cache-path'            => __DIR__ . '/_cache',
	'whitelist'             => [ __DIR__, dirname( __DIR__ ) . '/src' ],
	'redefinable-internals' => [ 'time' ],
	'env' => ['WordPress', 'MyPlugin'], // Load the built-in WordPress environment and the 'MyPlugin' custom one.
] );
```

Testing environments solve the problem of avoiding duplication, in the shipped code or in the tests for the shipped code, of functions and methods that are not subject to test but that are part of the environment itself.  
As an example it *might* happen to have to stub, mock or spy the `apply_filters` method in a small number of test methods but, for the most part, `apply_filters` will just be part of the **infrastructure** of any WordPress plugin/theme and will be **assumed** to be defined.  
There is no way for Function Mocker to keep up with all the needs of all the testing setups and all the code that's out there so Function Mocker comes with [its own environment generation tool](environment-generation.md).
