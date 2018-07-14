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

> The `\` followed by newline in the command example above is just a marker to break the lines; you could write the whole command on one line.

The command will generate, in this folder:

* a `tests/envs/woocommerce/functions.php` containing the copied signature, documentation and body of all the functions found in the source file
* a `tests/envs/woocommerce/boostrap.php` file that will just include all the environment files
* a `generation-config.json` file that reports the configuration used for this first generation

