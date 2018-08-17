# WooCommerce environment generation example

## Usage
If you did not already navigate to the repository root folder and install the package dependencies:
```bash
composer install
```
Back to this folder, install [Composer](https://getcomposer.org/) dependencies:
```bash
composer install
```
Run the tests:
```bash
vendor/bin/phpunit
```

## Generating a testing environment
In this example I'm writing a [WooCommerce plugin](https://github.com/woocommerce/woocommerce) add-on that will figure out which type of box, in a pre-defined set of boxes, could be used to pack a product I'm selling on my site.  
Since I'm working in the realm of examples I'm not worrying about the UI for the time being, but want to write some unit tests for the `Boxer` class.  
The class will take a product post `ID` as an input and return a "box" (an instance of the `Box` class) that can accomodate that product.  
You can have a look at the code in the `src` folder, but what's most important in the context of environment generation is that the `Box` and `Boxer` classes both depend on code defined by the WooCommerce plugin:

* the `WC_Product` class is WooCommerce product model
* the `wc_get_dimension` function will convert an input dimension from one length unit to another
* the `wc_get_weight` function will convert an input weight from one weight unit to another

To avoid having to rely on environments completely I *could* write stubs for the `WC_Product` and the functions but that would mean:

* keeping my `WC_Product` stub up to date with the original code as I code
* replicating/duplicating the utility code the two functions provide

Another option would be to `include` the files defining the `WC_Product` class and functions in the tests bootstrap file but that would run into the issue of WordPress lacking an autoload method and amount, essentially, to loading the plugin.  
Since I'm decided upon a better solution I will use Function Mocker built-in environment generation command to automate the generation.  
From the root folder of my project (the folder containing this `README.md` file) I can launch the `function-mocker` CLI tool:

```shell
../../function-mocker generate:env woocommerce ./vendor/woocommerce/woocommerce/includes
```

The first parameter, `woocommerce`, is the name of the environment, the second parameter is where functions and classes should be read from.  
The command will try to process all the files and folders in the source and will, unless, your PHP CLI binary has unlimited memory and time available, fail.  
The reason is that parsing and tokenizing all those files will easily consume a lot of memory; as the error suggests:

```shell
The command has consumed almost all the available PHP memory: use more stringent criteria for the source to avoid this.
```

Relatively to this folder the two functions are defined in the `./vendor/woocommerce/woocommerce/includes/wc-formatting-functions.php` while the `WC_Product` class is defined in the `./vendor/woocommerce/woocommerce/includes/abstracts/abstract-wc-product.php` file.  
As the error from the previous run suggested I'm narrowing down the scope of the import by specifying the two files as source:

```shell
../../function-mocker generate:env woocommerce \
	./vendor/woocommerce/woocommerce/includes/wc-formatting-functions.php \
	./vendor/woocommerce/woocommerce/includes/abstracts/abstract-wc-product.php	
```

The command will generate, in this folder, the following files:

* `tests/envs/woocommerce/boostrap.php` - the environment bootstrap file will include all the environment files one by one
* `tests/envs/woocommerce/functions.php` - contains the copied signature, documentation and body of all the functions found in the source file
* `tests/envs/woocommerce/generation-config.json` - reports the configuration used for this first generation
* `tests/envs/woocommerce/WC_Product.php` - contains a copy of the `WC_Product` class code

Taking a look at the code I can see that the `WC_Product` class has been copied and, in the same way, all the functions found in the file have been copied as well.  
Since I will not need all of them I update the `generation-config.json` file to specify that I only want to get two specific functions; the environment generation command has already done the job for me and I just need to remove excess lines:

```json
{
    "_readme": [
        "This file defines the woocommerce testing environment generation rules.",
        "Read more about it at https://github.com/lucatume/function-mocker.",
        "This file was automatically @generated."
    ],
    "timestamp": 1532613689,
    "date": "2018-07-26 14:01:29 (UTC)",
    "name": "woocommerce",
    "source": [
        "../../../vendor/woocommerce/woocommerce/includes/wc-formatting-functions.php",
        "../../../vendor/woocommerce/woocommerce/includes/abstracts/abstract-wc-product.php"
    ],
    "removeDocBlocks": false,
    "wrapInIf": true,
    "body": "copy",
    "functions": {
        "wc_get_dimension": {
            "removeDocBlocks": false,
            "body": "copy",
            "wrapInIf": true,
            "source": "../../../vendor/woocommerce/woocommerce/includes/wc-formatting-functions.php"
        },
        "wc_get_weight": {
            "removeDocBlocks": false,
            "body": "copy",
            "wrapInIf": true,
            "source": "../../../vendor/woocommerce/woocommerce/includes/wc-formatting-functions.php"
        }
    },
    "classes": {
        "WC_Product": {
            "removeDocBlocks": false,
            "body": "copy",
            "wrapInIf": true,
            "autoload": true
        }
    }
}
```

Now I run the environment creation command again specifying, this time, the configuration file to use:

```bash
../../function-mocker generate:env woocommerce \
	--config tests/envs/woocommerce/generation-config.json
```

Mind that I'm not specifying the sources anymore as those are specified in the configuration file.  
Since the configuration file is also specifying what classes and functions I want the environment to contain then those, and only those, will be imported in the new version of the environment files.  
When I run PHPUnit, though, an error will block its execution:

```bash
Fatal error:  Class 'WC_Abstract_Legacy_Product' not found in /Users/luca/Repos/function-mocker/examples/woocommerce-env/tests/envs/woocommerce/WC_Product.php on line 20
```

The `WC_Product` class is, in fact, extending the `WC_Abstract_Legacy_Product` one and that's exactly what PHP is complaining about.  
On that same note my testing environment will need the `WC_Data` class too.  
To fix the issue I can add the missing classes to the `classes` section of the config file and run the environment generation command again including the folders where the required classes are defined, like this:

```json
{
    "_readme": [
        "This file defines the woocommerce testing environment generation rules.",
        "Read more about it at https://github.com/lucatume/function-mocker.",
        "This file was automatically @generated."
    ],
    "timestamp": 1533539761,
    "date": "2018-08-06 07:16:01 (UTC)",
    "name": "woocommerce",
    "source": [
        "../../../vendor/woocommerce/woocommerce/includes/legacy",
        "../../../vendor/woocommerce/woocommerce/includes/abstracts",
        "../../../vendor/woocommerce/woocommerce/includes/wc-formatting-functions.php",
        "../../../vendor/woocommerce/woocommerce/includes/abstracts/abstract-wc-product.php"
    ],
    "bootstrap": "bootstrap.php",
    "removeDocBlocks": false,
    "body": "copy",
    "functions": {
        "wc_get_dimension": {
            "removeDocBlocks": false,
            "body": "copy",
            "wrapInIf": true,
            "source": "../../../vendor/woocommerce/woocommerce/includes/wc-formatting-functions.php"
        },
        "wc_get_weight": {
            "removeDocBlocks": false,
            "body": "copy",
            "wrapInIf": true,
            "source": "../../../vendor/woocommerce/woocommerce/includes/wc-formatting-functions.php"
        },
        "": {
            "removeDocBlocks": false,
            "body": "copy",
            "wrapInIf": true,
            "source": "../../../vendor/woocommerce/woocommerce/includes/wc-formatting-functions.php"
        }
    },
    "classes": {
        "WC_Product": {
            "removeDocBlocks": false,
            "body": "copy",
            "wrapInIf": true,
            "autoload": true
        },
        "WC_Abstract_Legacy_Product": {
            "removeDocBlocks": false,
            "body": "copy",
            "wrapInIf": true,
            "autoload": true
        },
        "WC_Data": {
            "removeDocBlocks": false,
            "body": "copy",
            "wrapInIf": true,
            "autoload": true
        }
    }
}
```

I run the command again adding the folder that contains WooCommerce abstract classes to the sources, the `./vendor/woocommerce/woocommerce/includes/legacy` folder:

```bash
../../function-mocker generate:env woocommerce \
	--config tests/envs/woocommerce/generation-config.json \
	./vendor/woocommerce/woocommerce/includes/legacy \
	./vendor/woocommerce/woocommerce/includes/abstracts
```

The environment was updated and the code of the `WC_Abstract_Legacy_Product` and `WC_Data` classes has been copied into the `tests/envs/woocommerce/` folder.  
Tests are now ready to run with a bare-bones, no real WordPress or WooCommerce needed, testing environment.  
To avoid this manual process I can leverage, alternatively, the `--with-dependencies` command option.  

### Automatically resolving and pulling in dependencies when generating environments
Starting from the step before the last one of the example above I found out some of the environment code I'm trying to generate, requires more code.  
Specifically the `WC_Product` class requires the `WC_Abstract_Legacy_Product` and `WC_Data` classes to be defined.  
With attention I can have the environment generation command "resolve and pull" those dependencies for me using the `--with-dependencies` command option.  
Given this configuration file:

```json
{
    "_readme": [
        "This file defines the woocommerce testing environment generation rules.",
        "Read more about it at https://github.com/lucatume/function-mocker.",
        "This file was automatically @generated."
    ],
    "timestamp": 1532613689,
    "date": "2018-07-26 14:01:29 (UTC)",
    "name": "woocommerce",
    "source": [
        "../../../vendor/woocommerce/woocommerce/includes/wc-formatting-functions.php",
        "../../../vendor/woocommerce/woocommerce/includes/abstracts/abstract-wc-product.php"
    ],
    "removeDocBlocks": false,
    "wrapInIf": true,
    "body": "copy",
    "functions": {
        "wc_get_dimension": {
            "removeDocBlocks": false,
            "body": "copy",
            "wrapInIf": true,
            "source": "../../../vendor/woocommerce/woocommerce/includes/wc-formatting-functions.php"
        },
        "wc_get_weight": {
            "removeDocBlocks": false,
            "body": "copy",
            "wrapInIf": true,
            "source": "../../../vendor/woocommerce/woocommerce/includes/wc-formatting-functions.php"
        }
    },
    "classes": {
        "WC_Product": {
            "removeDocBlocks": false,
            "body": "copy",
            "wrapInIf": true,
            "autoload": true
        }
    }
}
```

I run the environment creation command again specifying, this time, that I would like dependencies to be resolved for me:

```bash
../../function-mocker generate:env woocommerce \
	--config tests/envs/woocommerce/generation-config.json \
	--with-dependencies
```

## Using the testing environment and running the tests
Now that my WooCommerce testing environment is ready it's time to load it in Function Mocker.  
By default Function Mocker will always load a base WordPress testing environment defining the common utility functions (`add_filter`, `add_action`, l10n functions et cetera) if no `env` parameter is specified but I want it to load the one I just generated as well.  
To do so I will update the tests bootstrap file, the one where `FunctionMocker::init` is being called, to include the `WordPress` environment and then the generated one:

```php
<?php
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// init Function Mocker
\tad\FunctionMocker\FunctionMocker::init( [
	'cache-path'            => __DIR__ . '/../../../../_cache/fm-woocommerce-env-example',
	'env' => [ 
		'WordPress',
		__DIR__ . '/envs/woocommerce/bootstrap.php',
	],
] );
```

The environments will be loaded in the specified order.
Testing environments will be whitelisted by Function Mocker, there is no need to specify them in the `whitelist` entry when calling the `FunctionMocker::init` method.

## Customizing the environment generation using the configuration file
Whenever I want to update the testing environment I will simply run the command above again modifying, if required, the configuration file.  
Reading the configuration file from top to bottom here are the customization options available:

* `source` - string|array - this is a list, or a single entry, indicating the source files and folders that should be parsed to import functions, classes, traits and interfaces. The source files or folder paths should be **relative to the folder containing the configuration file**. When additional sources are specified in the CLI command those will be added to the ones listed in the configuration file.  
* `removeDocBlocks` - boolean - whether to remove doc-blocks from functions, class, trait and interface methods or not. This is an environment-wide setting that can be overridden in each function, class, trait or interface entry. Defaults to `false`; can be overridden in each function/class entry.
* `wrapInIf` - boolean - whether to wrap each function, class, interface or trait declaration in `if` existence checks or not. For functions the check will be made, on the function fully-qualified name, with `function_exists`; for classes the check will be made, on the class fully-qualified name with `class_exists`; for traits the check will be made, on the trait fully-qualified name, with `trait_exists`; on interfaces the check will be made, on the interface fully qualified name, with `interface_exists`. Defaults to `true`; can be overridden in each function/class entry..
* `body` - string - how the body of functions, class, or traits methods should be filled. The default setting, `copy`, will copy the original function/method body as it is; the `empty` setting will empty any function/method of its content leaving the method signature intact; the `throw` setting will fill the body of any function/method with a `throw` statement leaving the method signature intact; can be overridden in each function/class entry.  
* `autoload` - bool - whether classes/interfaces/traits should be autoloaded, via a generated autoloader, or not. When set to `false` then the environment bootstrap file will explicitly include the files. Defaults to `true`; can be overridden in each function/class entry.
* `functions` - object - a list specifying the functions that should be imported from the source files to the testing environment. When at least one function is specified then any other function that does not match the searched one **will not** be imported. The object keys should be the function fully-qualified name: global functions will have keys like `global_function` while namespaced functions will have keys like `Acme\\Company\\some_function`. Each function entry can specify settings overriding the environment-wide ones; the `source` property will not be used during generation and is only printed in the file for reference purposes.
* `classes` - object - a list specifying the classes, interfaces and traits that should be imported from the source files to the testing environment. When at least one class is specified then any other class that does not match the searched one **will not** be imported. The object keys should be the class fully-qualified name: global classes will have keys like `GlobalClass` while namespaced classes will have keys like `Acme\\Company\\SomeClass`. Each class entry can specify settings overriding the environment-wide ones; the `source` property will not be used during generation and is only printed in the file for reference purposes.
* `author` - string - the author that should be used in the `@author` tags in the file, should be in the `John Doe <john@example.com>` format.
* `copyright` - string - the copyright string that should be used in the `@copyright` tags in the file, should be in the `2018 John Doe` format.

Function entries can defined values to override the defaults:

* `removeDocBlocks` - boolean - whether to remove doc-blocks from the function declaration.
* `wrapInIf` - boolean - whether to wrap the function declaration in `if` existence checks or not. For functions the check will be made, on the function fully-qualified name, with `function_exists`.
* `body` - string - how the body of the function should be filled. The default setting, `copy`, will copy the original function/method body as it is; the `empty` setting will empty any function/method of its content leaving the method signature intact; the `throw` setting will fill the body of any function/method with a `throw` statement leaving the method signature intact.
* `fileName` - string - by default functions will be written to a `functions.php` file (in a namespace-dedicated folder if namespaced); setting this value allows specifying the name of the file the function should be written to; e.g. `filters.php`.

In a similar fashion class entries can be customized to override the default settings:

* `removeDocBlocks` - boolean - whether to remove doc-blocks from the class/trait/interface declaration and methods or not.
* `wrapInIf` - boolean - whether to wrap each class, interface or trait declaration in `if` existence checks or not made using the `class_exists`, `trait_exists` or `interface_exists` function.  
* `body` - string - how the body class or traits methods should be filled. The default setting, `copy`, will copy the original function/method body as it is; the `empty` setting will empty any function/method of its content leaving the method signature intact; the `throw` setting will fill the body of any function/method with a `throw` statement leaving the method signature intact.
* `autoload` - bool - whether class/interface/trait should be autoloaded, via a generated autoloader, or not. When set to `false` then the environment bootstrap file will explicitly include the files. Defaults to `true`.

Any other entry in the configuration file are just informative and will not be parsed from the command.
