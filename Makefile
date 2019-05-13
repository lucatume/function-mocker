SRC?=src

cs_sniff:
	vendor/bin/phpcs --colors -p --standard=phpcs.xml $(SRC) \
		--ignore=src/data,src/includes,src/tad/scripts,src/tad/FunctionMocker/envs \
		-s src

cs_fix:
	vendor/bin/phpcbf --colors -p --standard=phpcs.xml $(SRC) \
		--ignore=src/data,src/includes,src/tad/scripts,tests/cli/__snapshots__,src/tad/FunctionMocker/envs,examples/**/vendor \
		-s src tests examples

cs_fix_n_sniff: cs_fix cs_sniff

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

generate_autoloaded_wordpress:
	./function-mocker generate:env AutoloadedWordPress \
		${CURDIR}/vendor/wordpress/wordpress \
		--destination=${CURDIR}/AutoloadedWordPress \
		--author="WordPress Contributors <wordpress@wordpress.org>" \
		--copyright="WordPress Contributors" \
		--with-dependencies

# Builds the Docker-based parallel-lint util.
docker/parallel-lint/id:
	docker build --force-rm --iidfile docker/parallel-lint/id docker/parallel-lint --tag lucatume/parallel-lint:5.6

# Lints the source files with PHP Parallel Lint, requires the parallel-lint:5.6 image to be built.
lint: docker/parallel-lint/id
	docker run --rm -v ${CURDIR}:/app lucatume/parallel-lint:5.6 --colors /app/src
	docker run --rm -v ${CURDIR}:/app lucatume/parallel-lint:5.6 --colors /app/tests

duplicate_gitbook_files:
	cp ${CURDIR}/docs/welcome.md ${CURDIR}/docs/README.md

test:
	phpunit

pre_commit:lint test cs_sniff
