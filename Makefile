sniff:
	vendor/bin/phpcs --colors -p --standard=ruleset.xml src

fix:
	vendor/bin/phpcbf --colors -p --standard=ruleset.xml src

fix_n_sniff:
	vendor/bin/phpcbf --colors -p --standard=ruleset.xml src
	vendor/bin/phpcs --colors -p --standard=ruleset.xml src

install:
	composer install
	(cd examples/codeception; composer install)
	(cd examples/phpspec; composer install)
	(cd examples/phpunit; composer install)
	(cd examples/woocommerce-env; composer install)
	(cd examples/wp-browser; composer install)
	(cd examples/wp-core-suite; composer install)

update:
	composer update
	(cd examples/codeception; composer update)
	(cd examples/phpspec; composer update)
	(cd examples/phpunit; composer update)
	(cd examples/woocommerce-env; composer update)
	(cd examples/wp-browser; composer update)
	(cd examples/wp-core-suite; composer update)

wpenv:
	./function-mocker generate:env WordPress \
		--config=src/tad/FunctionMocker/envs/WordPress/generation-config.json \
		--with-dependencies
