all:
	make lint
	make test
	make analyse

lint:
	./vendor/bin/php-cs-fixer fix

analyse:
	./vendor/bin/phpstan analyse

test:
	php artisan test