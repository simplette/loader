all: clean install test phpstan

clean:
	rm ./composer.lock
	rm -rf ./vendor/*

install:
	composer install

test:
	vendor/bin/phpunit

phpstan:
	vendor/bin/phpstan analyse src --level 7
