SRC?=src

cs_sniff:
	vendor/bin/phpcs --colors -p --standard=ruleset.xml $(SRC)

cs_fix:
	vendor/bin/phpcbf --colors -p --standard=ruleset.xml $(SRC)

cs_fix_n_sniff:
	vendor/bin/phpcbf --colors -p --standard=ruleset.xml $(SRC)
	vendor/bin/phpcs --colors -p --standard=ruleset.xml $(SRC)

composer_install:
	composer install
	(cd examples/codeception; composer install)
	(cd examples/phpspec; composer install)
	(cd examples/phpunit; composer install)
	(cd examples/woocommerce-env; composer install)
	(cd examples/wp-browser; composer install)
	(cd examples/wp-core-suite; composer install)

composer_update:
	composer update
	(cd examples/codeception; composer update)
	(cd examples/phpspec; composer update)
	(cd examples/phpunit; composer update)
	(cd examples/woocommerce-env; composer update)
	(cd examples/wp-browser; composer update)
	(cd examples/wp-core-suite; composer update)

generate_wpenv:
	./function-mocker generate:env WordPress \
		--config=$(SRC)/tad/FunctionMocker/envs/WordPress/generation-config.json \
		--with-dependencies

# Builds the Docker-based parallel-lint util.
docker/parallel-lint/id:
	docker build --force-rm --iidfile docker/parallel-lint/id docker/parallel-lint --tag lucatume/parallel-lint:5.6

# Lints the source files with PHP Parallel Lint, requires the parallel-lint:5.6 image to be built.
lint: docker/parallel-lint/id
	docker run --rm -v ${CURDIR}:/app lucatume/parallel-lint:5.6 --colors /app/src
	docker run --rm -v ${CURDIR}:/app lucatume/parallel-lint:5.6 --colors /app/tests
